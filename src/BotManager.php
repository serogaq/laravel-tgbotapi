<?php

namespace Serogaq\TgBotApi;

use Illuminate\Support\Facades\Hash;
use Serogaq\TgBotApi\Exceptions\BotManagerConfigException;
use Serogaq\TgBotApi\Exceptions\BotManagerException;

class BotManager {
    private static ?object $botConf = null;

    private static array $bots = [];

    public static function selectBot(string $username): ?Bot {
        $bots = config('tgbotapi.bots', null);
        if ($bots === null) {
            throw new BotManagerConfigException('Bots array missing in tgbotapi config');
            return null;
        }
        $key = array_search($username, array_column($bots, 'username'));
        if ($key === false) {
            throw new BotManagerConfigException("Bot with username '{$username}' not found in tgbotapi config");
            return null;
        }
        $bot = $bots[$key];
        if (!isset($bot['token']) || !is_string($bot['token'])) {
            throw new BotManagerConfigException("Bot with username '{$username}' has a token issue");
            return null;
        }
        self::$botConf = (object) $bot;
        if (!isset(self::$botConf->log_channel) || !is_string(self::$botConf->log_channel) || self::$botConf->log_channel === 'default') {
            self::$botConf->log_channel = config('logging.default', 'single');
        }
        if (!isset(self::$botConf->api_server) || !is_string(self::$botConf->api_server)) {
            self::$botConf->api_server = config('tgbotapi.api_server', 'https://api.telegram.org');
        }
        return self::getBot();
    }

    public static function selectBotByHash(string $hash): ?Bot {
        $bots = config('tgbotapi.bots', null);
        if ($bots === null) {
            throw new BotManagerConfigException('Bots array missing in tgbotapi config');
            return null;
        }
        $bot = null;
        foreach ($bots as $b) {
            if (Hash::check($b['token'], $hash)) {
                $bot = $b;
                break;
            }
        }
        if (is_null($bot)) {
            throw new BotManagerConfigException("Bot with hash '{$hash}' not found in tgbotapi config");
            return null;
        }
        if (!isset($bot['token']) || !is_string($bot['token'])) {
            throw new BotManagerConfigException("Bot with username '{$username}' has a token issue");
            return null;
        }
        self::$botConf = (object) $bot;
        if (!isset(self::$botConf->log_channel) || !is_string(self::$botConf->log_channel) || self::$botConf->log_channel === 'default') {
            self::$botConf->log_channel = config('logging.default', 'single');
        }
        if (!isset(self::$botConf->api_server) || !is_string(self::$botConf->api_server)) {
            self::$botConf->api_server = config('tgbotapi.api_server', 'https://api.telegram.org');
        }
        return self::getBot();
    }

    private static function getBotConf(): ?object {
        if (is_null(self::$botConf)) {
            throw new BotManagerException('Bot not selected');
            return null;
        }
        return self::$botConf;
    }

    private static function getBot(): ?Bot {
        $botConf = self::getBotConf();
        if (is_null($botConf)) {
            return null;
        }
        if (isset(self::$bots[$botConf->username])) {
            return self::$bots[$botConf->username];
        }
        $bot = new Bot($botConf);
        self::$bots[$botConf->username] = $bot;
        return $bot;
    }
}
