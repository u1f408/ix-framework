<?php
declare(strict_types=1);
namespace ix\Database;

class RedisContainer {
	/** @var ?\Redis $instance */
	private static $instance = null;

	public static function get(): \Redis {
		if (self::$instance === null) {
			// Parse the REDIS_URL environment variable
			$params = self::parse_url($_ENV['REDIS_URL']);

			// Connect to Redis
			self::$instance = new \Redis();
			self::$instance->connect($params['host'], $params['port']);
			self::$instance->select($params['dbindex']);
		}

		return self::$instance;
	}

	/**
	 * @param string $url
	 * @return array<string, mixed>
	 */
	public static function parse_url(string $url): array {
		/** @var array<string, mixed> $params */
		$params = ['host' => '127.0.0.1', 'port' => 6379, 'dbindex' => 0];

		// TODO: make this betterer
		if (($url = parse_url($url)) !== false) {
			$params['host'] = $url['host'] ?? '127.0.0.1';
			$params['port'] = intval($url['port'] ?? 6379);
			$params['dbindex'] = intval(ltrim(strval($url['path'] ?? ''), '/') ?? 0);
		}

		return $params;
	}
}
