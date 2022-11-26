<?php

namespace Serogaq\TgBotApi\Helpers\Matchers;

use Serogaq\TgBotApi\Updates\CommandUpdate;

class Command extends SimpleXMLElement {
    const WITHOUT_ARGS = 0;

    const WITH_ARGS = 1;

    const WITH_SPACE_ARGS = 2;

    const WITH_UNDERSCORE_ARGS = 3;

    private static function prepareXml(?string $command, CommandUpdate $update, int $args): string {
        if (preg_match('#^/(?<command>[a-zA-Z0-9_]+)(@[a-zA-Z0-9_]+bot)?(?<data> .*)?$#msu', $update['message']['text'], $matches, PREG_OFFSET_CAPTURE) === 1) {
            $matchCommand = $matches['command'][0];
            if ($args === self::WITHOUT_ARGS) {
                if ($command === $matchCommand) {
                    return '<command>1</command>';
                } else {
                    return '<command/>';
                }
            } elseif ($args === self::WITH_ARGS) {
                $data = [];
                if ($command === $matchCommand || (mb_strlen($matchCommand) > mb_strlen($command) && str_contains($matchCommand, $command) && isset($matches['data']) && !empty(trim($matches['data'][0])))) {
                    $data[' '] = trim($matches['data'][0]);
                }
                if (mb_strlen($matchCommand) > mb_strlen($command) && str_contains($matchCommand, $command) && str_contains(mb_substr($matchCommand, mb_strlen($command)), '_')) {
                    $data['_'] = explode('_', mb_substr($matchCommand, mb_strlen($command) + 1));
                }
                if (!empty($data)) {
                    return '<!--' . htmlentities(json_encode($data)) . '--><command>1</command>';
                } else {
                    return '<command/>';
                }
            } elseif ($args === self::WITH_SPACE_ARGS) {
                if ($command === $matchCommand) {
                    if (isset($matches['data']) && !empty(trim($matches['data'][0]))) {
                        return '<!--' . htmlentities(json_encode([' ' => trim($matches['data'][0])])) . '--><command>1</command>';
                    } else {
                        return '<command>1</command>';
                    }
                } else {
                    return '<command/>';
                }
            } elseif ($args === self::WITH_UNDERSCORE_ARGS) {
                if (mb_strlen($matchCommand) > mb_strlen($command) && str_contains($matchCommand, $command) && str_contains(mb_substr($matchCommand, mb_strlen($command)), '_')) {
                    return '<!--' . htmlentities(json_encode(['_' => explode('_', mb_substr($matchCommand, mb_strlen($command) + 1))])) . '--><command>1</command>';
                } else {
                    return '<command/>';
                }
            } else {
                // TODO: throw Exception(Unknown command args match type)
            }
        } else {
            return '<command/>';
        }
    }

    public function getMatches(): array|string|null {
        preg_match("#<!\-\-(.+?)\-\->#", $this->asXML(), $matches);
        if (!$matches) {
            return null;
        }
        $result = json_decode(html_entity_decode($matches[1]), true);
        if (count($result) === 1) {
            return $result[' '] ?? $result['_'];
        }
        return $result;
    }

    public static function is(?string $command, CommandUpdate $update, int $args = self::WITHOUT_ARGS) {
        $xml = self::prepareXml($command, $update, $args);
        return new Command($xml);
    }
}
