<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Helpers;

/**
 * Getting the bot ID from its token.
 *
 * @param  array  $config
 */
function getBotIdFromToken(?string $token): ?int {
    if (empty($token)) return null;
    $explode = explode(':', $token);
    if (count($explode) !== 2) {
        return null;
    }
    $botId = $explode[0];
    if (mb_strlen($botId) < 7 || mb_strlen($botId) > 13 || !is_numeric($botId))  {
        return null;
    }
    return (int) $botId;
}

function arrayToObject(array $array): object {
    return json_decode(json_encode($array), false);
}

function isValidBotConfig(?array $config): bool {
    return (
        isset($config['username'], $config['token']) &&
        mb_strtolower(mb_substr($config['username'], -3)) === 'bot' &&
        !is_null(getBotIdFromToken($config['token'])) &&
        mb_strlen(explode(':', $config['token'])[1]) === 36
    ) ? true : false;
}
