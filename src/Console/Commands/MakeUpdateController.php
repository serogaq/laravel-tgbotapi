<?php

namespace Serogaq\TgBotApi\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeUpdateController extends GeneratorCommand {
    
    protected $selectedType;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tgbotapi:controller';

    protected $description = 'Create a new TgBotApi update controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Update Controller';

    public function handle() {
        $updateTypes = [
            'Update',
            'CallbackQueryUpdate',
            'ChannelPostUpdate',
            'ChosenInlineResultUpdate',
            'CommandUpdate',
            'ContactUpdate',
            'DiceUpdate',
            'EventUpdate',
            'GameUpdate',
            'InlineQueryUpdate',
            'LocationUpdate',
            'MediaUpdate',
            'PollUpdate',
            'PreCheckoutQueryUpdate',
            'ShippingQueryUpdate',
            'StickerUpdate',
            'TextUpdate',
            'VenueUpdate'
        ];
        $this->selectedType = $this->choice('Update Type', $updateTypes, 0);
        parent::handle();
        $this->doOtherOperations();
    }

    public function getStub() {
        return  __DIR__ . '/stubs/UpdateController.stub';
    }

    public function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\TgBotApi\Updates';
    }

    protected function doOtherOperations() {
        $class = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($class);
        $content = file_get_contents($path);
        file_put_contents($path, $content);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [];
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput() {
        return $this->selectedType;
    }
}
