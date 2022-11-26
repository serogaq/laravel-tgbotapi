<?php

namespace Serogaq\TgBotApi\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeMiddleware extends GeneratorCommand {
    public $selectedType;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tgbotapi:middleware {name}';

    protected $description = 'Create a new TgBotApi middleware class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Middleware';

    public function handle() {
        $this->selectedType = $this->choice('Middleware Type', ['RequestMiddleware', 'ResponseMiddleware'], 0);
        parent::handle();
        $this->doOtherOperations();
    }

    public function getStub() {
        return  __DIR__ . "/stubs/{$this->selectedType}.stub";
    }

    public function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\TgBotApi\Middleware';
    }

    protected function doOtherOperations() {
        $class = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($class);
        $content = file_get_contents($path);
        file_put_contents($path, $content);
    }
}
