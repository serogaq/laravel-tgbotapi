<?php

namespace Serogaq\TgBotApi\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Serogaq\TgBotApi\BotApi;
use Serogaq\TgBotApi\ApiRequest;
use Serogaq\TgBotApi\Facades\BotManager;
use \Throwable;

class GetUpdates extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tgbotapi:getupdates {username : Bot Username} {--once} {--until-complete} {--sleep=0.1}';

    protected $description = 'Getting bot updates';

    /**
     * Whether the loop should run.
     */
    protected bool $run = true;

    /**
     * Whether the work is complete.
     */
    protected bool $complete = false;

    /**
     * The number of seconds to sleep for.
     */
    protected float $sleep = 1;

    /**
     * The iteration number.
     */
    protected int $runs = 1;

    /**
     * Whether the work has started or not.
     */
    protected bool $started = false;

    /**
     * Unique id of the current iteration.
     */
    protected string $batchId;

    private ?BotApi $botApi;

    private string $username;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Command setup to run before the work loop starts.
     *
     * @return void
     */
    public function setUp(): void {
        $this->username = $this->argument('username');
        $this->logChannel = BotManager::getBotConfig($this->username)['log_channel'] ?? config('logging.default');
        $this->botApi = BotManager::bot($this->username);
        if (is_null($this->botApi)) {
            $this->error("Bot '{$username}' not found in tgbotapi config");
            exit(1);
        }
    }

    /**
     * The work to do during the loop.
     *
     * @return void
     */
    public function work(): void {
        if ($this->shouldRunUntilComplete() && $this->runs > 500 && Carbon::now()->second >= 57) {
            $this->completed();
            return;
        }
        try {
            $this->botApi->getUpdatesAndCreateEvents(['timeout' => 59], [
                ApiRequest::TIMEOUT => 70,
                ApiRequest::CONNECT_TIMEOUT => 10
            ]);
        } catch(Throwable $e) {
            report($e);
            $this->error('Something went wrong during work().');
            $this->error($e->getMessage());
        }
    }

    /**
     * Work to do after the loop ends.
     *
     * @return void
     */
    public function tearDown(): void {
        $this->line('tearDown');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->listenForSignals();
        $this->sleep = $this->getSleep();
        try {
            $this->setUp();
        } catch (Throwable $t) {
            $this->error('Something went wrong during setUp().');
            $this->error($t->getMessage());
            return 1;
        }
        $this->printInfo();
        $this->started = true;
        while ($this->run) {
            $this->getBatchId();
            $this->work();
            unset($this->batchId);
            if (!$this->shouldContinue()) {
                $this->run = false;
                continue;
            }
            $this->runs++;
            $this->sleep();
        }
        try {
            $this->tearDown();
        } catch (Throwable $t) {
            $this->error('Something went wrong during tearDown().');
            $this->error($t->getMessage());
            return 1;
        }
        $this->line('Stopping daemon');
    }

    /**
     * Listen for system signals.
     *
     * @return void
     */
    protected function listenForSignals(): void {
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, [$this, 'shutdown']);
        pcntl_signal(SIGTERM, [$this, 'shutdown']);
    }

    /**
     * Shutdown gracefully
     *
     * @return void
     */
    public function shutdown(): void {
        $this->run = false;
    }

    /**
     * Get the amount of time to sleep in seconds.
     *
     * @return int
     */
    private function getSleep(): float {
        $sleep = (float) $this->option('sleep');
        return $sleep * 1000000;
    }

    public function line($string, $style = null, $verbosity = null) {
        $formatted = Carbon::now()->format('[Y-m-d H:i:s.v] - ') . 'TgBotApi GetUpdatesCommand';
        if ($this->started && $this->run) {
            $formatted .= ':' . $this->getBatchId();
        }
        $formatted .= ' - ' . $string;
        parent::line($formatted, $style, $verbosity);
        Log::channel($this->logChannel)->debug('TgBotApi GetUpdatesCommand:' . $this->getBatchId() . "\n" . $string);
    }

    /**
     * Get the batch id.
     *
     * @return string
     */
    protected function getBatchId(): string {
        if (!isset($this->batchId)) {
            $this->batchId = mb_substr(md5(random_bytes(5)), 0, 7);
        }
        return $this->batchId;
    }

    /**
     * Print startup info to the console.
     *
     * @return void
     */
    protected function printInfo(): void {
        $this->line('Starting daemon');
        if ($this->shouldRunOnce()) {
            $this->line('Note: running once due to the --once flag');
        }
        if ($this->shouldRunUntilComplete() && !$this->shouldRunOnce()) {
            $this->line('Note: running until marked as complete due to the --until-complete flag');
        }
    }

    /**
     * Sleep at the end of the loop.
     *
     * @return void
     */
    protected function sleep(): void {
        usleep($this->sleep);
    }

    protected function shouldContinue(): bool {
        if ($this->shouldRunOnce() && $this->runs === 1) {
            return false;
        }
        if ($this->shouldRunUntilComplete() && $this->complete) {
            return false;
        }
        return true;
    }

    /**
     * Determine if the loop should only run once.
     *
     * @return bool
     */
    private function shouldRunOnce(): bool {
        if ($this->option('once') === true) {
            return true;
        }
        return false;
    }

    /**
     * Determine if the loop should run until complete.
     *
     * @return bool
     */
    private function shouldRunUntilComplete(): bool {
        if ($this->option('until-complete') === true) {
            return true;
        }
        return false;
    }

    /**
     * Mark the work as completed.
     *
     * @return void
     */
    protected function completed(): void {
        $this->complete = true;
    }
}
