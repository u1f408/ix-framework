<?php
declare(strict_types=1);
namespace ix\Session;

use \ix\Session\Session;
use \EasyCSRF\Interfaces\SessionProvider;

class EasyCSRFSessionProvider implements SessionProvider {
	/** @var Session $session */
	public $session;

	/**
	 * Construct a new EasyCSRFSessionProvider from the provided Session.
	 *
	 * @param Session $session
	 */
	public function __construct(Session $session) {
		$this->session = $session;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		// Pls can has string key
		$key = strval($key);

		// Return null if we have no session ID
		if (empty($this->session->session_id)) {
			return null;
		}
		
		// Return null if $key is not in the session data
		if (!array_key_exists($key, $this->session->session_data)) {
			return null;
		}

		// Get and return the actual value
		return $this->session->session_data[$key];
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		// Pls can has string key & value
		list($key, $value) = array_map('strval', [$key, $value]);

		// Ensure that we have a valid session, and retrieve the session data
		$this->session->ensure_create()->retrieve();

		// Set this $key to $value
		$this->session->session_data[$key] = $value;

		// Force a flush of the session content to Redis
		$this->session->update();
	}
}
