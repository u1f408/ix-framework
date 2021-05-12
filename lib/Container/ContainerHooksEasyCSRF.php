<?php
declare(strict_types=1);
namespace ix\Container;

// use \ix\Session\Session;
use \ix\Container\Container;
use \EasyCSRF\EasyCSRF;

final class ContainerHooksEasyCSRF {
	/**
	 * Container hook to add an EasyCSRF object as `csrf`, using cookie storage.
	 *
	 * NOTE: in this mode, the CSRF tokens are sent to the client in cookies,
	 * and do not have the relative safety of being stored in the Redis-backed
	 * \ix\Session\Session object.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param Container $container The Container instance
	 * @return Container The Container instance
	 */
	public static function hookContainerEasyCSRFCookie(array $key, Container $container) {
		$container->set('csrf', function() {
			$session_provider = new \EasyCSRF\NativeCookieProvider();
			return new EasyCSRF($session_provider);
		});

		return $container;
	}
}
