<?php

namespace Serogaq\TgBotApi\Updates;

use Serogaq\TgBotApi\Constants\MediaType;

final class MediaUpdate extends Update {
    protected int $typeFlags = 0;

    public function __construct(array $update) {
        parent::__construct($update);
        $this->detectMediaTypes();
    }

    public function getMediaTypeFlags(): int {
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

    private function detectMediaTypes(): void {
        if (isset($this->update['message']['photo'])) {
            $this->addFlag(MediaType::PHOTO);
        }
        if (isset($this->update['message']['video'])) {
            $this->addFlag(MediaType::VIDEO);
        }
        if (isset($this->update['message']['video_note'])) {
            $this->addFlag(MediaType::VIDEO_NOTE);
        }
        if (isset($this->update['message']['voice'])) {
            $this->addFlag(MediaType::VOICE);
        }
        if (isset($this->update['message']['document'])) {
            $this->addFlag(MediaType::DOCUMENT);
        }
    }
}
