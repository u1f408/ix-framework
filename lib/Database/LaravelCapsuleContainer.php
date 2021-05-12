<?php
declare(strict_types=1);
namespace ix\Database;

use IrisHelpers\DsnParser;
use Illuminate\Database\Capsule\Manager as Capsule;

class LaravelCapsuleContainer {
	/** @var Capsule $instance */
	private static $instance = null;

	public static function get(): Capsule {
		if (self::$instance === null) {
			// Parse the DATABASE_DSN environment variable
			$options = DsnParser::forLaravelDatabase()->parse($_ENV['DATABASE_DSN']);

			// Connect to the database
			self::$instance = new Capsule();
			self::$instance->addConnection($options);
			self::$instance->setAsGlobal();
			self::$instance->bootEloquent();
		}

		return self::$instance;
	}
}
