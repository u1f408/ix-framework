<?php
declare(strict_types=1);
namespace ix\Container;

use \ix\Session\SessionContainer;
use \ix\Container\Container;
use \EasyCSRF\EasyCSRF;

final class ContainerHooksSession {
	/**
	 * Container hook to add an a Session instance as `session`.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param Container $container The Container instance
	 * @return Container The Container instance
	 */
	public static function hookContainerSession(array $key, Container $container) {
		$container->set('session', function() {
			return SessionContainer::get();
		});

		return $container;
	}
}
