<?php
declare(strict_types=1);
namespace ix\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \ix\Controller\Controller;
use \ix\Helpers\HtmlRenderer;

final class ControllerHookInvalidCSRFTokenErrorPage {
	/**
	 * Controller hook to render a basic error page on a CSRF exception.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param mixed[] $params Array of [Controller, Request, Response]
	 * @return mixed[] Array of [Controller, Request, Response]
	 */
	public static function hookControllerInvalidCSRFToken(array $key, array $params): array {
		/** @var Controller $controller */
		/** @var Request $request */
		/** @var Response $response */
		list($controller, $request, $response) = $params;
		$html = new HtmlRenderer();

		$response->getBody()->write($html->renderDocument(
			[
				$html->tag('meta', ['charset' => 'utf-8']),
				$html->tag('meta', ['name' => 'viewport', 'content' => 'initial-scale=1, width=device-width']),
				$html->tag('link', ['rel' => 'stylesheet', 'href' => '/styles.css']),
				$html->tagHasChildren('title', [], 'CSRF verification error'),
			],
			[
				$html->tagHasChildren('div', ['class' => 'main error popup-container'], ...[
					$html->tagHasChildren('h1', [], 'CSRF verification error'),
					$html->tagHasChildren('p', [], 'Please refresh the page and try your action again.'),
				]),
			],
		));

		return [$controller, $request, $response];
	}
}