<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Exceptions\BotManagerConfigException;

class SetWebhook extends Command {
    protected $signature = 'tgbotapi:setwebhook {username : Bot Username} {--url= : Webhook URL}';

    protected $description = 'Setting up a webhook for a bot';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $username = $this->argument('username');
        try {
            $bot = BotManager::selectBot($username);
        } catch(BotManagerConfigException $e) {
            $this->error($e->getMessage());
            return 1;
        }
        $res = $bot->getWebhookInfo();
        $this->info('Current Webhook Info');
        foreach ($res as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
        $url = $this->option('url');
        if (is_null($url)) {
            while (true) {
                $host = $this->ask('Enter the application domain (for ex.: https://example.com)');
                $hash = Hash::make($bot->getBotConf()->token);
                $url = "{$host}/tgbotapi/webhook/{$hash}";
                if (!$this->confirm("The URL for the webhook will be used: {$url}", true)) {
                    continue;
                }
                break;
            }
        }
        $data = ['url' => $url];
        if ($res->url !== '') {
            if ($this->confirm('Drop Pending Updates?', false)) {
                $data['drop_pending_updates'] = true;
            }
        }
        $res = $bot->setWebhook($data);
        $res = $bot->getWebhookInfo();
        $this->info('New Webhook Info');
        foreach ($res as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
    }
}
