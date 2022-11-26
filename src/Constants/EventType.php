<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Constants;

class EventType {

    const NEW_CHAT_MEMBERS = 1;
    const LEFT_CHAT_MEMBER = 2;
    const PINNED_MESSAGE = 4;
    const EDITED_MESSAGE = 8;
    const EDITED_CHANNEL_POST = 16;
    const CHAT_JOIN_REQUEST = 32;
    const CHAT_MIGRATE = 64;
    const FORUM_TOPIC_CREATED = 128;
    const FORUM_TOPIC_CLOSED = 256;
    const FORUM_TOPIC_REOPENED = 512;
    const BOT_LEFT = 1024;
    const BOT_JOINED = 2048;
    const BOT_BLOCKED = 4096; // Your bot in private
    const BOT_UNBLOCKED = 16384; // Your bot in private
    const CHANGE_ADMINISTRATOR = 32768;
    const CHANGE_RESTRICTED = 65536;
    const CHANGE_KICKED = 131072;
    const ANY = 253951;

}