<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi;

use function Serogaq\TgBotApi\Helpers\getBotIdFromToken;

class BotManager {
    
    /**
     * Create a new class BotManager instance.
     *
     * @param  array  $config
     */
    public function __construct(protected array $config) {}

    /**
     * Checking that the bot is exists in the config.
     *
     * @param  int|string  $idOrUsername Bot ID or Bot Username
     * @return  bool  Bot existence flag in config
     */
    public function botExists(int|string $idOrUsername): bool {
        foreach ($this->config['bots'] as $bot) {
            if (
                (is_int($idOrUsername) && getBotIdFromToken($bot['token']) === $idOrUsername) ||
                (is_string($idOrUsername) && $bot['username'] === $idOrUsername)
            ) {
                return true;
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
        if(!$this->botExists($idOrUsername)) return null;
        foreach ($this->config['bots'] as $bot) {
            if (
                (is_int($idOrUsername) && getBotIdFromToken($bot['token']) === $idOrUsername) ||
                (is_string($idOrUsername) && $bot['username'] === $idOrUsername)
            ) {
                return $bot;
            }
        }
        return null;
    }

    /**
     * Getting the bot.
     *
     * @param  int|string  $idOrUsername Bot ID or Bot Username
     * @return  ?BotApi  BotApi instance
     */
    public function bot(int|string $idOrUsername): ?BotApi {
        if(!$this->botExists($idOrUsername)) return null;
        $botConfig = $this->getBotConfig($idOrUsername);
        return new BotApi($botConfig);
    }
}
