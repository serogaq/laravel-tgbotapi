<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Exceptions\BotManagerConfigException;

class DeleteWebhook extends Command {
    protected $signature = 'tgbotapi:deletewebhook {username : Bot Username}';

    protected $description = 'Removing a webhook for a bot';

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
        if ($res->url === '') {
            $this->info('Webhook is not set');
            return;
        }
        $data = [];
        if ($this->confirm('Drop Pending Updates?', false)) {
            $data['drop_pending_updates'] = true;
        }
        if (!empty($data)) {
            $res = $bot->deleteWebhook($data);
        } else {
            $res = $bot->deleteWebhook();
        }
        dump($res);
    }
}
