<?php

namespace Serogaq\TgBotApi;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use Serogaq\TgBotApi\Exceptions\BotException;
use Serogaq\TgBotApi\Exceptions\BotRequestException;
use Serogaq\TgBotApi\Objects\Update;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

class Bot {

	private object $botConf;

	private object $offsetStore;

	public function __construct(object $botConf) {
		$this->botConf = $botConf;
		$this->offsetStore = $this->getOffsetStore(true);
	}

	public function __call(string $name, array $arguments): mixed {
		$data = null;
		$attachments = null;
		if(isset($arguments[0]) && Arr::isAssoc($arguments[0])) $data = $arguments[0];
		if(isset($arguments[1]) && Arr::isAssoc($arguments[1])) $attachments = $arguments[1];
		if(is_null($data) && is_null($attachments)) $response = $this->apiRequest($name);
		elseif(!is_null($data) && is_null($attachments)) $response = $this->apiRequest($name, $data, null);
		elseif(is_null($data) && !is_null($attachments)) $response = $this->apiRequest($name, null, $attachments);
		elseif(!is_null($data) && !is_null($attachments)) $response = $this->apiRequest($name, $data, $attachments);
		$r = $response->json();
		if(isset($r['ok']) && $r['ok'] === true && isset($r['result']) && count($r) === 2 && is_array($r['result'])) return $this->arrayToObject($r['result']);
		return $this->arrayToObject($r);
	}

	private function arrayToObject(array $data): object {
		$obj = new \stdClass;
		foreach($data as $k => $v) {
			if(strlen($k)) {
				if(is_array($v)) $obj->{$k} = $this->arrayToObject($v);
				else $obj->{$k} = $v;
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
		$botConf = clone $this->botConf;
		$httpClient = Http::withOptions([])->acceptJson()->timeout(600);
		Log::channel($botConf->log_channel)->debug('TgBotApi Bot apiRequest request', ['data' => $data, 'attachments' => $attachments]);
		if(!is_null($data)) {
			$httpClient->asMultipart();
			if(!is_null($attachments)) {
				foreach($attachments as $key => $path) $httpClient->attach($key, file_get_contents($path), explode('/', $path)[count(explode('/', $path))-1]);
			}
			$multipartData = [];
			foreach($data as $key => $value) {
				$val = $value;
				if(is_array($value)) $val = json_encode($value);
				$multipartData[] = ['name' => $key, 'contents' => (string)$val];
			}
			$response = $httpClient->post("{$botConf->api_server}/bot{$botConf->token}/{$method}", $multipartData);
		} else $response = $httpClient->post("{$botConf->api_server}/bot{$botConf->token}/{$method}");
		Log::channel($botConf->log_channel)->debug('TgBotApi Bot apiRequest response', ['status' => $response->status(), 'body' => $response->body()]);
		return $response->throw(function ($response, $e) {
			if($e instanceof RequestException) throw new BotRequestException($e);
		});
	}

	private function getOffsetStore(bool $create = false): object {
		$offsetStore = config('tgbotapi.offset_store', 'file');
		if($offsetStore === 'file') {
			if(empty(realpath(storage_path('tgbotapi')))) throw new \RuntimeException('Offset store is not configured. Execute php artisan tgbotapi:install');
			if($create) {
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
						if($this->offsetStorage->missing($this->username.".offset")) $this->set(0);
						else $this->updateOffset = $this->get(true);
					}
					public function get(bool $force = false): int {
						if($force) return (int)$this->offsetStorage->get($this->username.".offset");
						else {
							if($this->updateOffset !== 0) return $this->updateOffset;
							return (int)$this->offsetStorage->get($this->username.".offset");
						}
					}
					public function set(int|string $offset): void {
						$this->updateOffset = (int)$offset;
						$this->offsetStorage->put($this->username.".offset", (string)$offset);
					}
				};
			} else $store = $this->offsetStore;
			return $store;
		}

	}

	public function getUpdates(callable $callback, ?array $data = null): void {
		$d = [];
		if($this->getOffsetStore()->get() > 0) $d['offset'] = $this->getOffsetStore()->get()+1;
		$autoUpdateOffset = true;
		if(!is_null($data)) {
			if(isset($data['offset'])) $autoUpdateOffset = false;
			$d = array_merge($d, $data);
		}
		if(!empty($d)) $response = $this->apiRequest('getUpdates', $d);
		else $response = $this->apiRequest('getUpdates');
		if(!empty($response['result'])) {
			foreach($response['result'] as $update) {
				$u = new Update($update);
				$ok = $callback($this, $u);
				if($autoUpdateOffset && is_bool($ok) && $ok === true) $this->getOffsetStore()->set($update['update_id']);
				else break;
			}
		}
	}

	public function getUpdatesAndCreateEvents(?array $data = null): void {
		$this->getUpdates(function (&$bot, $update) {
			event(new NewUpdateReceived($bot, $update, Update::GETUPDATES));
			return true;
		}, $data);
	}

}
