<?php
declare(strict_types=1);
namespace ix\Session;

use ix\Session\Session;

class SessionContainer {
	/** @var Session $instance */
	private static $instance = null;

	public static function get(): Session {
		if (self::$instance === null) {
			self::$instance = new Session();
		}

		return self::$instance;
	}
}
