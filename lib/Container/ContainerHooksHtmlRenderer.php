<?php
declare(strict_types=1);
namespace ix\Container;

use \ix\Container\Container;
use \ix\Helpers\HtmlRenderer;

final class ContainerHooksHtmlRenderer {
	/**
	 * Container hook to add `\ix\Helpers\HtmlRenderer` at `html`.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param Container $container The Container instance
	 * @return Container The Container instance
	 */
	public static function hookContainerHtmlRenderer(array $key, Container $container): Container {
		$container->set('html', function() {
			return new HtmlRenderer();
		});

		return $container;
	}
}
