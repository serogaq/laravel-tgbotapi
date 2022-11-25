<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Updates;

use Serogaq\TgBotApi\Interfaces\Update as IUpdate;
use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Exceptions\UpdateException;
use function Serogaq\TgBotApi\Helpers\arrayToObject;

class Update implements IUpdate, \Stringable, \ArrayAccess {

    protected array $update;

    /**
     * Create a new class Update instance.
     *
     * @param  array  $update
     */
    public function __construct(array $update) {
        $this->update = $update;
    }
    
    public function __toString(): string {
        return json_encode([
            'type' => (new \ReflectionClass($this))->getShortName(),
            'update' => $this->update
        ]);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        // Cannot be modified
    }

    public function offsetExists(mixed $offset): bool {
        return isset($this->update[$offset]);
    }

    public function offsetUnset(mixed $offset): void {
        // Cannot be deleted
    }

    public function offsetGet(mixed $offset): mixed {
        return isset($this->update[$offset]) ? $this->update[$offset] : null;
    }

    public function asObject(): object {
        return arrayToObject($this->update);
    }

    public static function createUpdate(string $updateType, array $update): IUpdate {
        try {
            $updateInstance = App::makeWith($updateType, ['update' => $update]);
        } catch (\Twrowable $e) {
            report($e);
            throw new UpdateException('Invalid update class', 0, $e);
        }
        return $updateInstance;
    }

    public static function create(array $update): IUpdate {
        if (empty($update)) {
            throw new UpdateException('Empty update', 1);
        }
        if (isset($update['message'])) {
            if (isset($update['message']['entities']) && $update['message']['entities'][0]['type'] === 'bot_command') {
                return self::createUpdate(CommandUpdate::class, $update);
            } elseif (isset($update['message']['text'])) {
                return self::createUpdate(TextUpdate::class, $update);
            }
            if (isset($update['message']['photo']) || isset($update['message']['video']) || isset($update['message']['video_note']) || isset($update['message']['voice']) || isset($update['message']['document'])) {
                return self::createUpdate(MediaUpdate::class, $update);
            }
            if (isset($update['message']['game'])) {
                return self::createUpdate(GameUpdate::class, $update);
            }
            if (isset($update['message']['contact'])) {
                return self::createUpdate(ContactUpdate::class, $update);
            }
            if (isset($update['message']['dice'])) {
                return self::createUpdate(DiceUpdate::class, $update);
            }
            if (isset($update['message']['venue'])) {
                return self::createUpdate(VenueUpdate::class, $update);
            }
            if (isset($update['message']['location'])) {
                return self::createUpdate(LocationUpdate::class, $update);
            }
            if (isset($update['message']['sticker'])) {
                return self::createUpdate(StickerUpdate::class, $update);
            }
            if (isset($update['message']['new_chat_members']) || isset($update['message']['left_chat_member']) || isset($update['message']['new_chat_title']) || isset($update['message']['new_chat_photo']) || isset($update['message']['delete_chat_photo']) || isset($update['message']['group_chat_created']) || isset($update['message']['supergroup_chat_created']) || isset($update['message']['channel_chat_created']) || isset($update['message']['message_auto_delete_timer_changed']) || isset($update['message']['migrate_to_chat_id']) || isset($update['message']['migrate_from_chat_id']) || isset($update['message']['pinned_message']) || isset($update['message']['invoice']) || isset($update['message']['successful_payment']) || isset($update['message']['connected_website']) || isset($update['message']['proximity_alert_triggered']) || isset($update['message']['forum_topic_created']) || isset($update['message']['forum_topic_closed']) || isset($update['message']['forum_topic_reopened']) || isset($update['message']['video_chat_scheduled']) || isset($update['message']['video_chat_started']) || isset($update['message']['video_chat_ended']) || isset($update['message']['video_chat_participants_invited']) || isset($update['message']['web_app_data'])) {
                return self::createUpdate(EventUpdate::class, $update);
            }
        }
        if (isset($update['edited_channel_post']) || isset($update['edited_message']) || isset($update['chat_join_request']) || isset($update['my_chat_member'])) {
            return self::createUpdate(EventUpdate::class, $update);
        }
        if (isset($update['channel_post'])) {
            return self::createUpdate(ChannelPostUpdate::class, $update);
        }
        if (isset($update['callback_query'])) {
            return self::createUpdate(CallbackQueryUpdate::class, $update);
        }
        if (isset($update['callback_query'], $update['callback_query']['game_short_name'])) {
            return self::createUpdate(GameUpdate::class, $update);
        }
        if (isset($update['inline_query'])) {
            return self::createUpdate(InlineQueryUpdate::class, $update);
        }
        if (isset($update['chosen_inline_result'])) {
            return self::createUpdate(ChosenInlineResultUpdate::class, $update);
        }
        if (isset($update['pre_checkout_query'])) {
            return self::createUpdate(PreCheckoutQueryUpdate::class, $update);
        }
        if (isset($update['shipping_query'])) {
            return self::createUpdate(ShippingUpdate::class, $update);
        }
        if (isset($update['poll'])) {
            return self::createUpdate(PollUpdate::class, $update);
        }
        if (isset($update['poll_answer'])) {
            return self::createUpdate(PollUpdate::class, $update);
        }
        return self::createUpdate(Update::class, $update);
    }
    
}
