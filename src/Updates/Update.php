<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Updates;

use Serogaq\TgBotApi\Interfaces\Update as IUpdate;
use Serogaq\TgBotApi\Interfaces\HttpClient;

class Update implements IUpdate {

    /**
     * Create a new class Update instance.
     *
     * @param  ?string  $json
     * @param  \Serogaq\TgBotApi\Interfaces\HttpClient  $httpClient
     */
    public function __construct(?string $json, HttpClient $httpClient) {

    }
    
    public function __toString(): string {
        return json_encode($this->asArray());
    }

    public function asArray(): array {
        //
    }

    public function asObject(): object {
        //
    }
    

}
