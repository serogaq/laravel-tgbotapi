<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi;

use function Serogaq\TgBotApi\Helpers\{ getBotIdFromToken, isValidBotConfig };

class BotManager {
    /**
     * Create a new class BotManager instance.
     *
     * @param  array  $bots
     */
    public function __construct(protected array $bots = []) {
    }

    /**
     * Checking that the bot is exists in the config.
     *
     * @param  int|string  $idOrUsername Bot ID or Bot Username
     * @return  bool  Bot existence flag in config
     */
    public function botExists(int|string $idOrUsername): bool {
        foreach ($this->bots as $bot) {
            if (!is_array($bot) || empty($bot)) {
                continue;
            }
            if (
                (is_int($idOrUsername) && getBotIdFromToken($bot['token']) === $idOrUsername) ||
                (is_string($idOrUsername) && $bot['username'] === $idOrUsername)
            ) {
                return isValidBotConfig($bot);
            }
        }
        return false;
    }

    /**
     * Getting the bot config.
     *
     * @param  int|string  $idOrUsername Bot ID or Bot Username
     * @return  ?array  Bot Config
     */
    public function getBotConfig(int|string $idOrUsername): ?array {
        if (!$this->botExists($idOrUsername)) {
            return null;
        }
        foreach ($this->bots as $bot) {
            if (
                (is_int($idOrUsername) && getBotIdFromToken($bot['token']) === $idOrUsername) ||
                (is_string($idOrUsername) && $bot['username'] === $idOrUsername)
            ) {
                $config = $bot;
                if (!isset($bot['log_channel'])) {
                    $config['log_channel'] = 'null';
                }
                if (!isset($bot['middleware'])) {
                    $config['middleware'] = [];
                }
                if (!isset($bot['api_url'])) {
                    $config['api_url'] = null;
                }
                return $config;
            }
        }
    }

    /**
     * Getting the bot.
     *
     * @param  int|string  $idOrUsername Bot ID or Bot Username
     * @return  ?BotApi  BotApi instance
     */
    public function bot(int|string $idOrUsername): ?BotApi {
        if (!$this->botExists($idOrUsername)) {
            return null;
        }
        $botConfig = $this->getBotConfig($idOrUsername);
        return new BotApi($botConfig);
    }
}
