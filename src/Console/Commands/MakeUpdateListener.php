<?php

namespace Serogaq\TgBotApi\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeUpdateListener extends GeneratorCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tgbotapi:listener {name}';

    protected $description = 'Create a new TgBotApi update listener';

    /**
     * The type of class being generated.
     *
     * @var string
     */
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
