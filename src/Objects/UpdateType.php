<?php

namespace Serogaq\TgBotApi\Objects;

class UpdateType {
    const MESSAGE = 100000;

    const TEXT = 100001;

    const COMMAND = 101000;

    const COMMAND_WITHOUT_ARGS = 101001;

    const COMMAND_WITH_ARGS = 101100;

    const COMMAND_WITH_ARGS_SPACE = 101101;

    const COMMAND_WITH_ARGS_UNDERSCORE = 101102;

    const MEDIA = 102000;

    const PHOTO = 102001;

    const VIDEO = 102002;

    const VIDEO_NOTE = 102003;

    const VOICE = 102004;

    const DOCUMENT = 102004;

    const GAME = 103000;

    const CONTACT = 104000;

    const DICE = 105000;

    const VENUE = 106000;

    const LOCATION = 107000;

    const STICKER = 108000;

    const EVENT = 110000;

    const EVENT_NEW_CHAT_MEMBERS = 110001;

    const EVENT_LEFT_CHAT_MEMBER = 110002;

    const EVENT_PINNED_MESSAGE = 110003;

    const EVENT_EDITED_MESSAGE = 110004;

    const EVENT_EDITED_CHANNEL_POST = 110005;

    const EVENT_CHAT_JOIN_REQUEST = 110006;

    const EVENT_CHAT_MIGRATE = 110007;

    const EVENT_FORUM_TOPIC_CREATED = 110008;

    const EVENT_FORUM_TOPIC_CLOSED = 110009;

    const EVENT_FORUM_TOPIC_REOPENED = 110010;

    const EVENT_BOT_LEFT = 110011;

    const EVENT_BOT_JOINED = 110012;

    const EVENT_BOT_BLOCKED = 110013; // Your bot in private

    const EVENT_BOT_UNBLOCKED = 110014; // Your bot in private

    const EVENT_CHANGE_ADMINISTRATOR = 110015;

    const EVENT_CHANGE_RESTRICTED = 110016;

    const EVENT_CHANGE_KICKED = 110017;

    const CHANNEL_POST = 120000;

    const CALLBACK_QUERY = 130000;

    const INLINE_QUERY = 140000;

    const CHOSEN_INLINE_RESULT = 150000;

    const PRE_CHECKOUT_QUERY = 160000;

    const SHIPPING_QUERY = 170000;

    const POLL = 180000;

    const POLL_ANSWER = 180001;

    const OTHER = 999999;
}
