<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Objects\UpdateType;

trait ProcessingInControllers {
    /**
     * Handle the event.
     *
     * @param  Serogaq\TgBotApi\Events\NewUpdateReceived  $event
     * @return void
     */
    public function handle(NewUpdateReceived $event) {
        if (App::environment('local')) {
            Log::channel($event->bot->getBotConf()->log_channel)->debug('Handle NewUpdateReceived event', ['event' => $event]);
        }
        $rc = new \ReflectionClass(get_class($this));
        if ($event->update->isUpdateType(UpdateType::TEXT) && $rc->hasMethod('handleTextUpdate')) {
            $this->handleTextUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::COMMAND) && $rc->hasMethod('handleCommandUpdate')) {
            $this->handleCommandUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::MEDIA) && $rc->hasMethod('handleMediaUpdate')) {
            $this->handleMediaUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::GAME) && $rc->hasMethod('handleGameUpdate')) {
            $this->handleGameUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::CONTACT) && $rc->hasMethod('handleContactUpdate')) {
            $this->handleContactUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::VENUE) && $rc->hasMethod('handleVenueUpdate')) {
            $this->handleVenueUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::LOCATION) && $rc->hasMethod('handleLocationUpdate')) {
            $this->handleLocationUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::STICKER) && $rc->hasMethod('handleStickerUpdate')) {
            $this->handleStickerUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::EVENT) && $rc->hasMethod('handleEventUpdate')) {
            $this->handleEventUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::CALLBACK_QUERY) && $rc->hasMethod('handleCallbackQueryUpdate')) {
            $this->handleCallbackQueryUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::INLINE_QUERY) && $rc->hasMethod('handleInlineQueryUpdate')) {
            $this->handleInlineQueryUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::CHOSEN_INLINE_RESULT) && $rc->hasMethod('handleChosenInlineResultUpdate')) {
            $this->handleChosenInlineResultUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::PRE_CHECKOUT_QUERY) && $rc->hasMethod('handlePreCheckoutQueryUpdate')) {
            $this->handlePreCheckoutQueryUpdate($event);
        }
        if ($event->update->isUpdateType(UpdateType::OTHER) && $rc->hasMethod('handleOtherUpdate')) {
            $this->handleOtherUpdate($event);
        }
    }
}
