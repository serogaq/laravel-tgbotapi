<?php

namespace Serogaq\TgBotApi;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Exceptions\BotException;
use Serogaq\TgBotApi\Exceptions\BotRequestException;
use Serogaq\TgBotApi\Objects\Update;

class Bot {
    private object $botConf;

    private object $offsetStore;

    private bool $asyncNextRequest = false;

    private ?array $asyncRequest;

    public function __construct(object $botConf) {
        $this->botConf = $botConf;
        $this->offsetStore = $this->getOffsetStore(true);
    }

    public function __call(string $name, array $arguments = []): mixed {
        if ($name === 'getUpdates') {
            $this->asyncNextRequest = false;
        }
        if ($this->asyncNextRequest) {
            $this->asyncRequest[] = $this->getBotConf()->username;
            $this->asyncRequest[] = $name;
            $this->asyncRequest[] = $arguments;
            $jsonReq = json_encode($this->asyncRequest);
            $this->asyncRequest = null;
            $id = bin2hex(random_bytes(10));
            $asyncRequests = json_decode(Cache::store('octane')->get('tgbotapi_async_requests', '[]'), true);
            $asyncRequests[$id] = $jsonReq;
            Cache::store('octane')->forever('tgbotapi_async_requests', json_encode($asyncRequests));
            $this->asyncNextRequest = false;
            return $id;
        }
        $data = null;
        $attachments = null;
        if (isset($arguments[0]) && Arr::isAssoc($arguments[0])) {
            $data = $arguments[0];
        }
        if (isset($arguments[1]) && Arr::isAssoc($arguments[1])) {
            $attachments = $arguments[1];
        }
        if (is_null($data) && is_null($attachments)) {
            $response = $this->apiRequest($name);
        } elseif (!is_null($data) && is_null($attachments)) {
            $response = $this->apiRequest($name, $data, null);
        } elseif (is_null($data) && !is_null($attachments)) {
            $response = $this->apiRequest($name, null, $attachments);
        } elseif (!is_null($data) && !is_null($attachments)) {
            $response = $this->apiRequest($name, $data, $attachments);
        }
        $r = $response->json();
        if (isset($r['ok']) && $r['ok'] === true && isset($r['result']) && count($r) === 2 && is_array($r['result'])) {
            return $this->arrayToObject($r['result']);
        }
        return $this->arrayToObject($r);
    }

    private function arrayToObject(array $data): object {
        $obj = new \stdClass;
        foreach ($data as $k => $v) {
            if (mb_strlen($k)) {
                if (is_array($v)) {
                    $obj->{$k} = $this->arrayToObject($v);
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        return $obj;
    }

    public function getBotConf(): object {
        $botConf = clone $this->botConf;
        unset($botConf->token);
        return $botConf;
    }

    public function getBotId(): int {
        return explode(':', $this->botConf->token)[0];
    }

    private function apiRequest(string $method, ?array $data = null, ?array $attachments = null): Response {
        $uid = bin2hex(random_bytes(3));
        $botConf = clone $this->botConf;
        $httpClient = Http::withOptions([])->acceptJson()->timeout(600);
        Log::channel($botConf->log_channel)->debug('TgBotApi Bot apiRequest request[' . $uid . ']', ['data' => $data, 'attachments' => $attachments]);
        if (!is_null($data)) {
            $httpClient->asMultipart();
            if (!is_null($attachments)) {
                foreach ($attachments as $key => $path) {
                    $httpClient->attach($key, file_get_contents($path), explode('/', $path)[count(explode('/', $path)) - 1]);
                }
            }
            $multipartData = [];
            foreach ($data as $key => $value) {
                $val = $value;
                if (is_array($value)) {
                    $val = json_encode($value);
                }
                $multipartData[] = ['name' => $key, 'contents' => (string) $val];
            }
            $response = $httpClient->post("{$botConf->api_server}/bot{$botConf->token}/{$method}", $multipartData);
        } else {
            $response = $httpClient->post("{$botConf->api_server}/bot{$botConf->token}/{$method}");
        }
        Log::channel($botConf->log_channel)->debug('TgBotApi Bot apiRequest response[' . $uid . ']', ['status' => $response->status(), 'body' => $response->body()]);
        return $response->throw(function ($response, $e) {
            if ($e instanceof RequestException) {
                throw new BotRequestException($e);
            }
        });
    }

    private function getOffsetStore(bool $create = false): object {
        $offsetStore = config('tgbotapi.offset_store', 'file');
        if ($offsetStore === 'file') {
            if (empty(realpath(storage_path('tgbotapi')))) {
                throw new \RuntimeException('Offset store is not configured. Execute php artisan tgbotapi:install');
            }
            if ($create) {
                $store = new class($this->getBotConf()->username) {
                    public string $username;

                    public int $updateOffset = 0;

                    public $offsetStorage;

                    public function __construct(string $username) {
                        $this->username = $username;
                        $offsetStorage = Storage::build([
                            'driver' => 'local',
                            'root' => storage_path('tgbotapi'),
                        ]);
                        $this->offsetStorage = $offsetStorage;
                        if ($this->offsetStorage->missing($this->username . '.offset')) {
                            $this->set(0);
                        } else {
                            $this->updateOffset = $this->get(true);
                        }
                    }

                    public function get(bool $force = false): int {
                        if ($force) {
                            return (int) $this->offsetStorage->get($this->username . '.offset');
                        } else {
                            if ($this->updateOffset !== 0) {
                                return $this->updateOffset;
                            }
                            return (int) $this->offsetStorage->get($this->username . '.offset');
                        }
                    }

                    public function set(int|string $offset): void {
                        $this->updateOffset = (int) $offset;
                        $this->offsetStorage->put($this->username . '.offset', (string) $offset);
                    }
                };
            } else {
                $store = $this->offsetStore;
            }
            return $store;
        }
    }

    public function getUpdates(callable $callback, ?array $data = null): void {
        $d = [];
        if ($this->getOffsetStore()->get() > 0) {
            $d['offset'] = $this->getOffsetStore()->get() + 1;
        }
        $autoUpdateOffset = true;
        if (!is_null($data)) {
            if (isset($data['offset'])) {
                $autoUpdateOffset = false;
            }
            $d = array_merge($d, $data);
        }
        if (!empty($d)) {
            $response = $this->apiRequest('getUpdates', $d);
        } else {
            $response = $this->apiRequest('getUpdates');
        }
        if (!empty($response['result'])) {
            foreach ($response['result'] as $update) {
                $u = new Update($update);
                $ok = $callback($this, $u);
                if ($autoUpdateOffset && is_bool($ok) && $ok === true) {
                    $this->getOffsetStore()->set($update['update_id']);
                } else {
                    break;
                }
            }
        }
    }

    public function getUpdatesAndCreateEvents(?array $data = null): void {
        $this->getUpdates(function (&$bot, $update) {
            event(new NewUpdateReceived($bot, $update, Update::GETUPDATES));
            return true;
        }, $data);
    }

    public function async(): self {
        if (!defined('SWOOLE_VERSION') || (defined('SWOOLE_VERSION') && !class_exists('\Laravel\Octane\Facades\Octane'))) {
            throw new BotException('Swoole and \Laravel\Octane\Facades\Octane must be available to use this feature');
        }
        $this->asyncNextRequest = true;
        return $this;
    }

    public function countPendingAsyncRequests(): int {
        if (!defined('SWOOLE_VERSION') || (defined('SWOOLE_VERSION') && !class_exists('\Laravel\Octane\Facades\Octane'))) {
            throw new BotException('Swoole and \Laravel\Octane\Facades\Octane must be available to use this feature');
        }
        $asyncRequests = json_decode(Cache::store('octane')->get('tgbotapi_async_requests', '[]'), true);
        $count = 0;
        foreach ($asyncRequests as $id => $req) {
            $request = json_decode($req, true);
            if ($request[0] !== $this->botConf->username) {
                continue;
            }
            $count += 1;
        }
        return $count;
    }

    public function runAsyncRequests(): ?array {
        if (!defined('SWOOLE_VERSION') || (defined('SWOOLE_VERSION') && !class_exists('\Laravel\Octane\Facades\Octane'))) {
            throw new BotException('Swoole and \Laravel\Octane\Facades\Octane must be available to use this feature');
        }
        $countRequests = $this->countPendingAsyncRequests();
        if ($countRequests === 0) {
            return null;
        }
        $waitTimeout = $countRequests * 8000;
        if ($waitTimeout > 60000) {
            $waitTimeout = 60000;
        }
        $asyncRequests = json_decode(Cache::store('octane')->get('tgbotapi_async_requests', '[]'), true);
        $tasks = [];
        $skippedIds = [];
        foreach ($asyncRequests as $id => $req) {
            $request = json_decode($req, true);
            if ($request[0] !== $this->botConf->username) {
                $skippedIds[] = $id;
                continue;
            }
            $tasks[] = function () use ($id, $request) {
                $bot = \Serogaq\TgBotApi\BotManager::selectBot($request[0]);
                $response = call_user_func_array([$bot, $request[1]], $request[2]);
                return [$id => $response];
            };
        }
        $result = \Laravel\Octane\Facades\Octane::concurrently($tasks, $waitTimeout);
        if (empty($skippedIds)) {
            Cache::store('octane')->forget('tgbotapi_async_requests');
        } else {
            $updatedAsyncRequests = [];
            foreach ($asyncRequests as $id => $req) {
                if (in_array($id, $skippedIds)) {
                    $updatedAsyncRequests[$id] = $req;
                }
            }
            Cache::store('octane')->forever('tgbotapi_async_requests', json_encode($updatedAsyncRequests));
        }
        return $result;
    }
}
