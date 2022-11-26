<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Helpers;

/**
 * Getting the bot ID from its token.
 *
 * @param  array  $config
 */
function getBotIdFromToken(string $token): ?int {
    $explode = explode(':', $token);
    if (count($explode) !== 2) {
        return null;
    }
    $botId = $explode[0];
    if (mb_strlen($botId) < 7 || mb_strlen($botId) > 13 || !is_numeric($botId)) {
        return null;
    }
    return (int) $botId;
}

function arrayToObject(array $array): object {
    return json_decode(json_encode($array), false);
}
