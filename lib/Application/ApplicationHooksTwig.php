<?php
declare(strict_types=1);
namespace ix\Application;

use \ix\Application\Application;
use \Slim\Views\TwigMiddleware;

final class ApplicationHooksTwig {
	/**
	 * Application middleware hook to add a `\Slim\Views\TwigMiddleware'.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param \Slim\App $app The App instance
	 * @return \Slim\App The App instance
	 */
	public static function hookApplicationMiddlewareTwig(array $key, \Slim\App $app): \Slim\App {
		$app->add(TwigMiddleware::createFromContainer($app));
		return $app;
	}
}
