<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\GeneratorCommand;

class MakeUpdateProcessingListener extends GeneratorCommand {

	protected $hidden = true;
	
	protected $name = 'tgbotapi:makeupdatelistener';

	protected $description = 'Create a new update listener';

	protected $type = 'Listener';

	protected function getStub() {
		return __DIR__ . '/stubs/UpdateProcessing.php.stub';
	}

	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace . '\Listeners';
	}

	public function handle() {
		parent::handle();
		$this->doOtherOperations();
	}

	protected function doOtherOperations() {
		$class = $this->qualifyClass($this->getNameInput());
		$path = $this->getPath($class);
		$content = file_get_contents($path);
		file_put_contents($path, $content);
	}
}