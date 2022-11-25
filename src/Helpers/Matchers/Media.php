<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Helpers\Matchers;

use Serogaq\TgBotApi\Updates\MediaUpdate;

class Media  {

    /**
     * Checks if MediaUpdate matches any of the passed EventType's.
     * Example:
     * if (Media::anyOf(MediaType::PHOTO | MediaType::DOCUMENT))
     * if (Media::anyOf(MediaType::ALL ^ MediaType::VOICE)) // Everything, except MediaType::VOICE
     * 
     * @param int $flags 
     * @param MediaUpdate $update
     *
     * @return bool
     */
    public static function anyOf(int $flags, MediaUpdate $update): bool {
        $mediaTypeFlags = $update->getMediaTypeFlags();
        if($mediaTypeFlags & $flags) return true;
        return false;
    }

}