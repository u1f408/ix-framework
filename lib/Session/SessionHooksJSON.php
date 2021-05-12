<?php
declare(strict_types=1);
namespace ix\Session;

final class SessionHooksJSON {
	/**
	 * Session `retrieve` hook to parse the session key's value as JSON.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param string $value Raw session key data
	 * @return mixed The decoded JSON
	 */
	public static function hookRetrieveJson(array $key, string $value) {
		$sessionKeyName = end($key);
		return json_decode($value, true);
	}

	/**
	 * Session `update` hook to dump the session key's value to JSON.
	 *
	 * @param string[] $key Hook key (unused)
	 * @param mixed $value Raw session key data
	 * @return string The encoded JSON
	 */
	public static function hookUpdateJson(array $key, mixed $value) {
		$sessionKeyName = end($key);
		return ($output = json_encode($value)) === false ? "" : $output;
	}
}
