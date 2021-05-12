<?php
declare(strict_types=1);
namespace ix\Container;

use \ix\HookMachine;
use \DI\Container as DIContainer;

class Container extends DIContainer {
	public function __construct() {
		parent::__construct();
		
		// Execute construction hooks
		HookMachine::execute([self::class, 'construct'], $this);
	}
}
