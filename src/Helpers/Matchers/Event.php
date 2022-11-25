<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Helpers\Matchers;

use Serogaq\TgBotApi\Updates\EventUpdate;

class Event  {

    /**
     * Checks if EventUpdate matches any of the passed EventType's.
     * Example:
     * if (Event::anyOf(EventType::NEW_CHAT_MEMBERS | EventType::LEFT_CHAT_MEMBER))
     * 
     * @param int $flags 
     * @param EventUpdate $update
     *
     * @return bool
     */
    public static function anyOf(int $flags, EventUpdate $update): bool {
        $eventTypeFlags = $update->getEventTypeFlags();
        if($eventTypeFlags & $flags) return true;
        return false;
    }

    /**
     * Checks if EventUpdate matches all of the passed EventType's.
     * Example:
     * if (Event::allOf(EventType::NEW_CHAT_MEMBERS | EventType::BOT_JOINED))
     * 
     * @param int $flags 
     * @param EventUpdate $update
     *
     * @return bool
     */
    public static function allOf(int $flags, int $eventTypeFlags) {
        $eventTypeFlags = $update->getEventTypeFlags();
        if($eventTypeFlags === $flags) return true;
        return false;
    }

}