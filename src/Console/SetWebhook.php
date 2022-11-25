<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Serogaq\TgBotApi\Facades\BotManager;

class SetWebhook extends Command {
    protected $signature = 'tgbotapi:setwebhook {username : Bot Username} {--url= : Webhook URL}';

    protected $description = 'Setting up a webhook for a bot';

    public function handle() {
        $username = $this->argument('username');
        $botApi = BotManager::bot($username);
        if (is_null($botApi)) {
            $this->error("Bot '{$username}' not found in tgbotapi config");
            return null;
        }
        $res = $botApi->getWebhookInfo()->send();
        $this->info('Current Webhook Info');
        foreach ($res['result'] as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
        $url = $this->option('url');
        if (is_null($url)) {
            $firstLoop = true;
            while (true) {
                if (!$firstLoop) {
                    $url = $this->ask('Enter webhook url');
                } else {
                    $token = BotManager::getBotConfig($username)['token'];
                    $url = route('tgbotapi.webhook', ['token' => $token]);
                    $firstLoop = false;
                }
                if (!$this->confirm("The URL for the webhook will be used: {$url}", true)) {
                    continue;
                }
                break;
            }
        }
        $data = ['url' => $url];
        if ($res['result']['url'] !== '') {
            if ($this->confirm('Drop Pending Updates?', false)) {
                $data['drop_pending_updates'] = true;
            }
        }
        $botApi->setWebhook($data)->send();
        $res = $botApi->getWebhookInfo()->send();
        $this->info('New Webhook Info');
        foreach ($res['result'] as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
    }
}
