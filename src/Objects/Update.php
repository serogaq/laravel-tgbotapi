<?php

namespace Serogaq\TgBotApi\Objects;

use Serogaq\TgBotApi\Objects\UpdateType;
use Serogaq\TgBotApi\Exceptions\BotUpdateException;

class Update {

	const INTERNAL = 1;
	const WEBHOOK = 2;
	const GETUPDATES = 3;

	protected ?array $_update = null;
	protected array $_updateTypes = [];
	protected bool $_isMatch = false;
	protected array|string|null $_matches = null;
	
	public function __construct(?array $update = null) {
		$this->_update = $update;
		if(!is_null($update)) $this->assignUpdateType();
    }

	/*public function __serialize(): array {
		return $this->_update;
	}*/

	public function __toString(): string {
		return json_encode($this->_update);
	}

	/*public static function __set_state(array $properties): object {
		$obj = new Update($properties);
		return $obj;
	}*/

	public function __debugInfo(): array {
		return $this->_update;
    }

	public function __get(string $name): mixed {
		if(!is_null($this->_update) && isset($this->_update[$name])) {
			$property = $this->_update[$name];
			if(is_array($property)) $property = $this->arrayToObject($property);
			return $property;
		}
		// TODO: Exception
	}

	public function __isset(string $name): bool {
		if(!is_null($this->_update) && isset($this->_update[$name])) return true;
		return false;
	}

	private function arrayToObject(array $data): object {
		$obj = new \stdClass;
		foreach($data as $k => $v) {
			if(strlen($k)) {
				if(is_array($v)) $obj->{$k} = $this->arrayToObject($v);
				else $obj->{$k} = $v;
			}
		}
		return $obj;
	}

	protected function assignUpdateType(): void {
		if(is_null($this->_update)) return;
		if(isset($this->_update['message'])) {
			$this->_updateTypes[] = UpdateType::MESSAGE;
			if(isset($this->_update['message']['entities']) && $this->_update['message']['entities'][0]['type'] === 'bot_command') {
				$this->_updateTypes[] = UpdateType::COMMAND;
				$command = preg_replace('#^/(.+?)@\S+ #', '/$1 ', $this->_update['message']['text'], 1);
				//preg_match('#^/(\S+)#', $command, $match);
				if(strpos($command, ' ') === false && strpos($command, '_') === false) $this->_updateTypes[] = UpdateType::COMMAND_WITHOUT_ARGS;
				else {
					$this->_updateTypes[] = UpdateType::COMMAND_WITH_ARGS;
					if(strpos($command, ' ') !== false) $this->_updateTypes[] = UpdateType::COMMAND_WITH_ARGS_SPACE;
					preg_match('#^/(\S+_\S+)#', $command, $match);
					if(count($match) > 0) $this->_updateTypes[] = UpdateType::COMMAND_WITH_ARGS_UNDERSCORE;
				}
			} else if(isset($this->_update['message']['text'])) $this->_updateTypes[] = UpdateType::TEXT;
			if(isset($this->_update['message']['photo']) || isset($this->_update['message']['video']) || isset($this->_update['message']['video_note']) || isset($this->_update['message']['voice']) || isset($this->_update['message']['document'])) {
				$this->_updateTypes[] = UpdateType::MEDIA;
				if(isset($this->_update['message']['photo'])) $this->_updateTypes[] = UpdateType::PHOTO;
				if(isset($this->_update['message']['video'])) $this->_updateTypes[] = UpdateType::VIDEO;
				if(isset($this->_update['message']['video_note'])) $this->_updateTypes[] = UpdateType::VIDEO_NOTE;
				if(isset($this->_update['message']['voice'])) $this->_updateTypes[] = UpdateType::VOICE;
				if(isset($this->_update['message']['document'])) $this->_updateTypes[] = UpdateType::DOCUMENT;
			}
			if(isset($this->_update['message']['game'])) $this->_updateTypes[] = UpdateType::GAME;
			if(isset($this->_update['message']['contact'])) $this->_updateTypes[] = UpdateType::CONTACT;
			if(isset($this->_update['message']['venue'])) $this->_updateTypes[] = UpdateType::VENUE;
			if(isset($this->_update['message']['location'])) $this->_updateTypes[] = UpdateType::LOCATION;
			if(isset($this->_update['message']['sticker'])) $this->_updateTypes[] = UpdateType::STICKER;
			if(isset($this->_update['message']['new_chat_members']) || isset($this->_update['message']['left_chat_member']) || isset($this->_update['message']['new_chat_title']) || isset($this->_update['message']['new_chat_photo']) || isset($this->_update['message']['delete_chat_photo']) || isset($this->_update['message']['group_chat_created']) || isset($this->_update['message']['supergroup_chat_created']) || isset($this->_update['message']['channel_chat_created']) || isset($this->_update['message']['migrate_to_chat_id']) || isset($this->_update['message']['migrate_from_chat_id']) || isset($this->_update['message']['pinned_message']) || isset($this->_update['message']['invoice']) || isset($this->_update['message']['successful_payment'])) {
				$this->_updateTypes[] = UpdateType::EVENT;
				if(isset($this->_update['message']['new_chat_members'])) $this->_updateTypes[] = UpdateType::EVENT_NEW_CHAT_MEMBERS;
				if(isset($this->_update['message']['left_chat_member'])) $this->_updateTypes[] = UpdateType::EVENT_LEFT_CHAT_MEMBER;
				if(isset($this->_update['message']['pinned_message'])) $this->_updateTypes[] = UpdateType::EVENT_PINNED_MESSAGE;
			}
		}
		if(isset($this->_update['edited_channel_post']) || isset($this->_update['channel_post']) || isset($this->_update['edited_message'])) {
			$this->_updateTypes[] = UpdateType::EVENT;
			if(isset($this->_update['edited_message'])) $this->_updateTypes[] = UpdateType::EVENT_EDITED_MESSAGE;
		}
		if(isset($this->_update['callback_query'])) $this->_updateTypes[] = UpdateType::CALLBACK_QUERY;
		if(isset($this->_update['callback_query']) && isset($this->_update['callback_query']['game_short_name'])) $this->_updateTypes[] = UpdateType::GAME;
		if(isset($this->_update['inline_query'])) $this->_updateTypes[] = UpdateType::INLINE_QUERY;
		if(isset($this->_update['chosen_inline_result'])) $this->_updateTypes[] = UpdateType::CHOSEN_INLINE_RESULT;
		if(isset($this->_update['pre_checkout_query'])) $this->_updateTypes[] = UpdateType::PRE_CHECKOUT_QUERY;
		if(empty($this->_updateTypes)) $this->_updateTypes[] = UpdateType::OTHER;
	}

	public function isUpdateType(int $updateType): bool {
		return in_array($updateType, $this->_updateTypes, true);
	}

	public function isUpdateTypes(int ...$updateTypes): bool {
		foreach($updateTypes as $updateType) {
			if(!in_array($updateType, $this->_updateTypes, true)) return false;
		}
		return true;
	}

	public function getMatches(): array|string|null {
		return ($this->_isMatch ? $this->_matches : null);
	}

	public function textMatch(string $text): bool {
		if(is_null($this->_update)) throw new BotUpdateException('Update is null');
		$this->_matches = null;
		$this->_isMatch = false;
		$bool = (isset($this->_update['message']) && isset($this->_update['message']['text']) && $this->_update['message']['text'] === $text);
		$this->_isMatch = $bool;
		return $bool;
	}

	public function textRegexpMatch(string $regexp): bool {
		if(is_null($this->_update)) throw new BotUpdateException('Update is null');
		$this->_matches = null;
		$this->_isMatch = false;
		$bool = (isset($this->_update['message']) && isset($this->_update['message']['text']) && preg_match($regexp, $this->_update['message']['text'], $match));
		$this->_isMatch = $bool;
		$this->_matches = empty($match) ? null : $match;
		return $bool;
	}

	public function buttonMatch(string $text): bool {
		return $this->textMatch($text);
	}

	public function buttonRegexpMatch(string $regexp): bool {
		return $this->textRegexpMatch($regexp);
	}

	public function inlineButtonMatch(string $callbackData): bool {
		if(is_null($this->_update)) throw new BotUpdateException('Update is null');
		$this->_matches = null;
		$this->_isMatch = false;
		$bool = (isset($this->_update['callback_query']) && isset($this->_update['callback_query']['data']) && $this->_update['callback_query']['data'] === $callbackData);
		$this->_isMatch = $bool;
		return $bool;
	}

	public function inlineButtonRegexpMatch(string $regexp): bool {
		if(is_null($this->_update)) throw new BotUpdateException('Update is null');
		$this->_matches = null;
		$this->_isMatch = false;
		$bool = (isset($this->_update['callback_query']) && isset($this->_update['callback_query']['data']) && preg_match($regexp, $this->_update['callback_query']['data'], $match));
		$this->_isMatch = $bool;
		$this->_matches = empty($match) ? null : $match;
		return $bool;
	}

	public function commandMatch(string $command): bool {
		if(is_null($this->_update)) throw new BotUpdateException('Update is null');
		if(!isset($this->_update['message']) || !isset($this->_update['message']['text'])) return false;
		$cmd = preg_replace('#^/(.+?)@\S+ #', '/$1 ', $this->_update['message']['text'], 1);
		$this->_matches = null;
		if(strpos($cmd, ' ') !== false) {
			$this->_matches = mb_substr($cmd, strpos($cmd, ' ')+1);
			$cmd = mb_substr($cmd, 0, strpos($cmd, ' '));
		}
		$cmd = preg_replace('#^/(.+)#', '$1', $cmd, 1);
		$this->_isMatch = false;
		$bool = ($command === $cmd);
		$this->_isMatch = $bool;
		return $bool;
	}

	public function commandRegexpMatch(string $regexp): bool {
		if(is_null($this->_update)) throw new BotUpdateException('Update is null');
		if(!isset($this->_update['message']) || !isset($this->_update['message']['text'])) return false;
		$cmd = preg_replace('#^/(.+?)@\S+ #', '/$1 ', $this->_update['message']['text'], 1);
		$this->_matches = null;
		if(strpos($cmd, ' ') !== false) {
			$this->_matches['whitespace'] = mb_substr($cmd, strpos($cmd, ' ')+1);
			$cmd = mb_substr($cmd, 0, strpos($cmd, ' '));
		}
		$cmd = preg_replace('#^/(.+)#', '$1', $cmd, 1);
		$this->_isMatch = false;
		$bool = (preg_match($regexp, $cmd, $match) ? true : false);
		$this->_isMatch = $bool;
		if(is_null($this->_matches)) $this->_matches = empty($match) ? null : $match;
		else $this->_matches = array_merge($this->_matches, $match);
		return $bool;
	}

}
