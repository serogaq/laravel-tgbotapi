<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Storage;

class MakeUpdateController extends GeneratorCommand {
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
    protected $type = 'Update Controller';

    public function handle() {
        $names = [
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
            'VenueUpdate',
            'Update'
        ];
        $this->name = $this->argument('name');
        if (!in_array($this->name, $names)) {
            $this->line('  <bg=red> ERROR </> Argument <options=bold>Name</> must be one of: <options=bold>' . implode(', ', $names) . '</>');
            return 1;
        }
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
        $class = $this->qualifyClass($this->name);
        $path = $this->getPath($class);
        $content = file_get_contents($path);
        file_put_contents($path, $content);
    }
}
