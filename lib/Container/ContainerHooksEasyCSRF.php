<?php
declare(strict_types=1);
namespace ix\Container;

use \ix\Container\Container;
use \ix\Session\Session;
use \ix\Session\EasyCSRFSessionProvider;
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
	
	/**
	 * Container hook to add an EasyCSRF object as `csrf`, using Redis-backed
	 * storage via \ix\Session\Session.
	 *
	 * NOTE: You must also add the Session hook (`hookContainerSession` from
	 * `\ix\Container\ContainerHooksSession`) to the Container construction
 	 * for this hook to work at all.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param Container $container The Container instance
	 * @return Container The Container instance
	 */
	public static function hookContainerEasyCSRFSession(array $key, Container $container) {
		$container->set('csrf', function(Container $container) {
			$session_provider = new EasyCSRFSessionProvider($container->get('session'));
			return new EasyCSRF($session_provider);
		});

		return $container;
	}
}
