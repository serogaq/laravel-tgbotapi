<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Updates;

use Serogaq\TgBotApi\Constants\EventType;

final class EventUpdate extends Update {
    
    protected int $typeFlags = 0;

    public function __construct(array $update) {
        parent::__construct($update);
        $this->detectEventTypes();
    }

    public function getEventTypeFlags(): int {
        return $this->typeFlags;
    }

    private function addFlag(int $flag): self {
        $this->typeFlags |= $flag;
        return $this;
    }

    private function removeFlag(int $flag): self {
        $this->typeFlags &= ~$flag;
        return $this;
    }

    private function detectEventTypes(): void {
        if (isset($this->update['message']['new_chat_members'])) {
            $this->addFlag(EventType::NEW_CHAT_MEMBERS);
            $botJoined = false;
            foreach ($this->update['message']['new_chat_members'] as $member) {
                if ($member['is_bot']) {
                    $botJoined = true;
                }
            }
            if ($botJoined) {
                $this->addFlag(EventType::BOT_JOINED);
            }
        }
        if (isset($this->update['message']['left_chat_member'])) {
            $this->addFlag(EventType::LEFT_CHAT_MEMBER);
            if ($this->update['message']['left_chat_member']['is_bot']) {
                $this->addFlag(EventType::BOT_LEFT);
            }
        }
        if (isset($this->update['message']['pinned_message'])) {
            $this->addFlag(EventType::PINNED_MESSAGE);
        }
        if (isset($this->update['message']['migrate_to_chat_id']) || isset($this->update['message']['migrate_from_chat_id'])) {
            $this->addFlag(EventType::CHAT_MIGRATE);
        }
        if (isset($this->update['message']['forum_topic_created'])) {
            $this->addFlag(EventType::FORUM_TOPIC_CREATED);
        }
        if (isset($this->update['message']['forum_topic_closed'])) {
            $this->addFlag(EventType::FORUM_TOPIC_CLOSED);
        }
        if (isset($this->update['message']['forum_topic_reopened'])) {
            $this->addFlag(EventType::FORUM_TOPIC_REOPENED);
        }
        if (isset($this->update['edited_channel_post'])) {
            $this->addFlag(EventType::EDITED_CHANNEL_POST);
        }
        if (isset($this->update['edited_message'])) {
            $this->addFlag(EventType::EDITED_MESSAGE);
        }
        if (isset($this->update['chat_join_request'])) {
            $this->addFlag(EventType::CHAT_JOIN_REQUEST);
        }
        if (isset($this->update['my_chat_member'])) {
            if (isset($this->update['my_chat_member']['new_chat_member'], $this->update['my_chat_member']['old_chat_member'], $this->update['my_chat_member']['chat']) && $this->update['my_chat_member']['chat']['type'] === 'private') {
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'kicked') {
                    $this->addFlag(EventType::BOT_BLOCKED);
                }
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'member' && $this->update['my_chat_member']['old_chat_member']['status'] === 'kicked') {
                    $this->addFlag(EventType::BOT_UNBLOCKED);
                }
            } elseif (isset($this->update['my_chat_member']['new_chat_member'], $this->update['my_chat_member']['old_chat_member'])) {
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'administrator' || $this->update['my_chat_member']['old_chat_member']['status'] === 'administrator') {
                    $this->addFlag(EventType::CHANGE_ADMINISTRATOR);
                }
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'restricted' || $this->update['my_chat_member']['old_chat_member']['status'] === 'restricted') {
                    $this->addFlag(EventType::CHANGE_RESTRICTED);
                }
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'kicked' || $this->update['my_chat_member']['old_chat_member']['status'] === 'kicked') {
                    $this->addFlag(EventType::CHANGE_KICKED);
                }
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'member' && $this->update['my_chat_member']['new_chat_member']['user']['is_bot']) {
                    $this->addFlag(EventType::BOT_JOINED);
                }
                if ($this->update['my_chat_member']['new_chat_member']['status'] === 'left' && $this->update['my_chat_member']['new_chat_member']['user']['is_bot']) {
                    $this->addFlag(EventType::BOT_LEFT);
                }
            }
        }
    }

}
