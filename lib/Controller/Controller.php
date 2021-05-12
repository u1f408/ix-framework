<?php
declare(strict_types=1);
namespace ix\Controller;

use ix\HookMachine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpMethodNotAllowedException;

class Controller {
	/** @var ?ContainerInterface $container */
	public $container;

	/**
	 * @param ?ContainerInterface $container
	 */
	public function __construct(?ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * Invoke the controller's requestMETHOD function
	 *
	 * @param Request $request The Request object
	 * @param Response $response The Response object
	 * @param mixed[] $args Arguments passed from the router (if any)
	 * @return Response The resulting Response object
	 */
	public function __invoke(Request $request, Response $response, ?array $args = []): Response {
		// Check whether we have a method that can handle this request.
		// If we don't - throw an HTTP 405 exception.
		$reqMethod = "request{$request->getMethod()}";
		if (!method_exists($this, $reqMethod)) {
			throw new HttpMethodNotAllowedException($request);
		}

		// Run the preRequestMethod hooks
		list($_, $request, $response) = HookMachine::execute(
			[self::class, 'request', 'preRequestMethod'],
			[$this, $request, $response],
		);

		// Invoke request method, storing the response, handling exceptions
		try {
			$response = $this->$reqMethod($request, $response, $args);
		} catch (\EasyCSRF\Exceptions\InvalidCsrfTokenException $e) {
			// Invalid CSRF, run the invalidCsrfToken hooks
			list($_, $request, $response) = HookMachine::execute(
				[self::class, 'request', 'invalidCSRFToken'],
				[$this, $request, $response],
			);
		}

		// Run the postRequestMethod hooks
		list($_, $request, $response) = HookMachine::execute(
			[self::class, 'request', 'postRequestMethod'],
			[$this, $request, $response],
		);

		// And finally, return our response
		return $response;
	}
}
