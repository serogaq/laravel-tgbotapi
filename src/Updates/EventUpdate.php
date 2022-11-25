<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Updates;

use Serogaq\TgBotApi\Constants\UpdateEventType;

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
            $this->addFlag(UpdateEventType::NEW_CHAT_MEMBERS);
            $botJoined = false;
            foreach ($this->update['message']['new_chat_members'] as $member) {
                if ($member['is_bot']) {
                    $botJoined = true;
                }
            }
            if ($botJoined) {
                $this->addFlag(UpdateEventType::BOT_JOINED);
            }
        }
        if (isset($this->update['message']['left_chat_member'])) {
            $this->addFlag(UpdateEventType::LEFT_CHAT_MEMBER);
            if ($this->update['message']['left_chat_member']['is_bot']) {
                $this->addFlag(UpdateEventType::BOT_LEFT);
            }
        }
        if (isset($this->update['message']['pinned_message'])) {
            $this->addFlag(UpdateEventType::PINNED_MESSAGE);
        }
        if (isset($this->update['message']['migrate_to_chat_id']) || isset($this->update['message']['migrate_from_chat_id'])) {
            $this->addFlag(UpdateEventType::CHAT_MIGRATE);
        }
        if (isset($this->update['message']['forum_topic_created'])) {
            $this->addFlag(UpdateEventType::FORUM_TOPIC_CREATED);
        }
        if (isset($this->update['message']['forum_topic_closed'])) {
            $this->addFlag(UpdateEventType::FORUM_TOPIC_CLOSED);
        }
        if (isset($this->update['message']['forum_topic_reopened'])) {
            $this->addFlag(UpdateEventType::FORUM_TOPIC_REOPENED);
        }
    }

}
