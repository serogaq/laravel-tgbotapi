<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\GeneratorCommand;

class MakeTgBotApiController extends GeneratorCommand {
    public $name;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tgbotapi-controller {name}';

    protected $description = 'Create a new update controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TgBotApi Update Controller';

    public function handle() {
        $names = [
            'CallbackQueryUpdate',
            'ChosenInlineResultUpdate',
            'CommandUpdate',
            'ContactUpdate',
            'EventUpdate',
            'GameUpdate',
            'InlineQueryUpdate',
            'LocationUpdate',
            'MediaUpdate',
            'OtherUpdate',
            'PreCheckoutQueryUpdate',
            'StickerUpdate',
            'TextUpdate',
            'VenueUpdate',
        ];
        $this->name = $this->argument('name');
        if (!in_array($this->name, $names)) {
            $this->line('  <bg=red> ERROR </> <options=bold>Name</> must be one of: <options=bold>' . implode(', ', $names) . '</>');
            return 1;
        }
        parent::handle();
        $this->doOtherOperations();
    }

    public function getStub() {
        return  __DIR__ . '/stubs/MakeTgBotApiController.stub';
    }

    public function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Http\Controllers\TgBotApi';
    }

    protected function doOtherOperations() {
        $class = $this->qualifyClass($this->name);
        $path = $this->getPath($class);
        $content = file_get_contents($path);
        file_put_contents($path, $content);
    }
}
