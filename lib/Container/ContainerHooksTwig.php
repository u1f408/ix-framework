<?php
declare(strict_types=1);
namespace ix\Container;

use \ix\Container\Container;
use \Slim\Views\Twig;

final class ContainerHooksTwig {
	/**
	 * Container hook to add a `\Slim\Views\Twig` at `view`
	 *
	 * NOTE: You must also add the application middleware hook for Twig
	 * (`hookApplicationMiddlewareTwig` from `\ix\Application\ApplicationHooksTwig`)
	 * to `[\ix\Application\Application::class, 'create_app', 'preMiddleware']`
	 * for this hook to work properly.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param Container $container The Container instance
	 * @return Container The Container instance
	 */
	public static function hookContainerTwig(array $key, Container $container): Container {
		$container->set('view', function() {
			return Twig::create(
				array_filter([IX_BASE . '/templates']),
				[
					'cache' => IX_BASE . '/cache/templates',
				],
			);
		});

		return $container;
	}
}
	