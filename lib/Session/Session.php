<?php
declare(strict_types=1);
namespace ix\Session;

use ix\HookMachine;
use ix\Database\RedisContainer;

/**
 * A Redis-backed session.
 */
class Session {
	/**
	 * Derives a Redis key from the session ID.
	 *
	 * @param string $session_id The session ID
	 * @return string The derived Redis key
	 */
	public static function session_id_to_redis_key(string $session_id): string {
		return "ixsession:{$session_id}";
	}

	/** @var ?string $session_id The session ID, or null */
	public $session_id;
	
	/** @var array<string, mixed> $session_data The raw session data */
	public $session_data;

	/**
	 * Construct a new Session.
	 *
	 * @param ?string $session_id The session ID, or null
	 */
	public function __construct(?string $session_id = null) {
		if ($session_id === null) {
			if (array_key_exists(IX_ENVBASE . "_SESSIONCOOKIE", $_ENV)) {
				if (array_key_exists($_ENV[IX_ENVBASE . "_SESSIONCOOKIE"], $_COOKIE)) {
					$session_id = $_COOKIE[$_ENV[IX_ENVBASE . "_SESSIONCOOKIE"]];
				}
			}

			if (empty(trim($session_id ?? ''))) {
				$session_id = null;
			}
		}

		$this->set_session_id($session_id);
		$this->session_data = [];
	}

	/**
	 * Set the session ID of this Session.
	 *
	 * @param ?string $session_id The session ID, or null
	 * @return Session The Session object
	 */
	public function set_session_id(?string $session_id): self {
		if (empty($this->session_id = trim(strval($session_id ?? '')))) {
			$this->session_id = null;
		}

		return $this;
	}
	
	/**
	 * Generate a new session ID and store it for this Session, if this Session
	 * does not already have a valid session ID.
	 *
	 * @return Session The Session object
	 */
	public function ensure_create(): self {
		if ($this->session_id === null) {
			$this->session_id = bin2hex(random_bytes(24));
		}

		return $this;
	}

	/**
	 * Retrieve the session from Redis.
	 *
	 * @return Session The Session object
	 */
	public function retrieve(): self {
		if ($this->session_id !== null) {
			$session_key = self::session_id_to_redis_key($this->session_id);
			$redis = RedisContainer::get();

			if (0 < $redis->exists($session_key)) {
				$this->session_data = [];
				foreach ($redis->hGetAll($session_key) as $key => $value) {
					$key = strval($key);
					$value = HookMachine::execute([self::class, 'retrieve', $key], $value);
					$this->session_data[$key] = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Update Redis with this session's data, and refresh the session cookie.
	 *
	 * @return Session The Session object
	 */
	public function update(): self {
		if ($this->session_id !== null) {
			$session_expiry = intval(time() + (60 * 60 * 24 * 30)); // Expire session in 30 days
			$session_key = self::session_id_to_redis_key($this->session_id);
			$redis = RedisContainer::get();

			// Delete existing session data from Redis and repopulate
			$redis->del($session_key);
			foreach ($this->session_data as $key => $value) {
				$value = HookMachine::execute([self::class, 'update', $key], $value);
				$redis->hSet($session_key, $key, strval($value));
			}

			// Set session expiry
			$redis->expireAt($session_key, $session_expiry);

			// Update our session cookie
			setcookie($_ENV[IX_ENVBASE . "_SESSIONCOOKIE"], $this->session_id, [
				'expires' => $session_expiry,
				'path' => '/',
				'httponly' => true,
			]);
		}

		return $this;
	}
	
	/**
	 * Destroy the current session, removing it's data from Redis, and deleting
	 * the session cookie.
	 *
	 * @return void
	 */
	public function destroy(): void {
		if ($this->session_id !== null) {
			// Run destroy hooks for each key
			foreach ($this->session_data as $key => $value) {
				HookMachine::execute([self::class, 'destroy', $key], $value);
			}

			// Purge from Redis
			$redis = RedisContainer::get();
			$redis->del(self::session_id_to_redis_key($this->session_id));

			// Remove the cookie by blanking it and setting it to expire at the epoch
			setcookie($_ENV[IX_ENVBASE . "_SESSIONCOOKIE"], '', [
				'expires' => 0,
				'path' => '/',
				'httponly' => true,
			]);

			// And reset this Session instance
			$this->session_id = null;
			$this->session_data = [];
		}
	}
}
