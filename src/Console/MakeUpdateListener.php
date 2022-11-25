<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\GeneratorCommand;

class MakeUpdateListener extends GeneratorCommand {
    protected $name = 'tgbotapi:makeupdatelistener {name}';

    protected $description = 'Create a new update listener';

    protected $type = 'Update Listener';

    public function handle() {
        parent::handle();
        $this->doOtherOperations();
    }
    
    protected function getStub() {
        return __DIR__ . '/stubs/UpdateListener.stub';
    }

    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Listeners';
    }

    protected function doOtherOperations() {
        $class = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($class);
        $content = file_get_contents($path);
        file_put_contents($path, $content);
    }
}
