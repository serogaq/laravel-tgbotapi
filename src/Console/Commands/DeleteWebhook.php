<?php

namespace Serogaq\TgBotApi\Console\Commands;

use Illuminate\Console\Command;
use Serogaq\TgBotApi\Facades\BotManager;

class DeleteWebhook extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tgbotapi:deletewebhook {username : Bot Username}';

    protected $description = 'Removing a webhook for a bot';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $username = $this->argument('username');
        $botApi = BotManager::bot($username);
        if (is_null($botApi)) {
            $this->error("Bot '{$username}' not found in tgbotapi config");
            return 1;
        }
        $res = $bot->getWebhookInfo()->send();
        $this->info('Current Webhook Info');
        foreach ($res['result'] as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
        if ($res['result']['url'] === '') {
            $this->info('Webhook is not set');
            return;
        }
        $data = [];
        if ($this->confirm('Drop Pending Updates?', false)) {
            $data['drop_pending_updates'] = true;
        }
        if (!empty($data)) {
            $res = $botApi->deleteWebhook($data)->send();
        } else {
            $res = $botApi->deleteWebhook()->send();
        }
        dump($res);
    }
}
