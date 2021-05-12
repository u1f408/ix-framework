<?php
declare(strict_types=1);
namespace ix\Application;

use \ix\HookMachine;
use \ix\Container\Container;
use \DI\Bridge\Slim\Bridge as DIBridge;
use \Slim\App as SlimApp;

class Application {
	/**
	 * Create a new \Slim\App, using the hook machinery for configuration.
	 *
	 * @return SlimApp The configured application
	 */
	public function create_app(): SlimApp {
		// Set $_ENV['APP_ENV'] to 'production' if it's not set
		if (!array_key_exists('APP_ENV', $_ENV) || empty($_ENV['APP_ENV'])) {
			$_ENV['APP_ENV'] = 'production';
		}

		// Construct the container and the application
		$container = new Container();
		$app = DIBridge::create($container);

		// Run pre-middleware-addition hooks
		HookMachine::execute([self::class, 'create_app', 'preMiddleware'], $app);

		// Add the last of the middleware
		$app->addRoutingMiddleware();
		$app->addErrorMiddleware(
			in_array($_ENV['APP_ENV'], ['development', 'test']),
			true,
			true,
		);

		// Run post-middleware-addition hooks
		HookMachine::execute([self::class, 'create_app', 'postMiddleware'], $app);

		// Run route registration hooks
		HookMachine::execute([self::class, 'create_app', 'routeRegister'], $app);

		// Return the new application
		return $app;
	}
}
