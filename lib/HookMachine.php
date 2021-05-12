<?php
declare(strict_types=1);
namespace ix;

/**
 * HookMachine is a way for applications to register scoped "hook" functions
 * that framework classes can then call, as a way to allow for overriding
 * and extension of base implementation details.
 */
final class HookMachine {
	/** @var array<string, callable[]> $hooks; */
	public static $hooks = [];

 	/**
	 * Returns a canonicalized hook key, as a string, from the input
	 * hook key array. Used for indexing the `HookMachine::$hooks` array.
	 *
	 * @param string[] $key Hook key
	 * @return string Canonicalized hook key
	 */
	public static function canonicalize_hook_key(array $key): string {
		return implode('$', array_map('trim', array_map('strval', $key)));
	}

	/**
	 * Add a hook to the end of the stack for a given key.
	 *
	 * @param string[] $key Hook key
	 * @param callable $hook Hook function
	 * @return int Number of hooks registered for that key
	 */
	public static function add(array $key, $hook): int {
		$mKey = self::canonicalize_hook_key($key);
		self::$hooks[$mKey] =
			array_key_exists($mKey, self::$hooks)
			? array_values(self::$hooks[$mKey])
			: [];

		self::$hooks[$mKey][] = $hook;
		return count(self::$hooks[$mKey]);
	}

	/**
	 * Execute the hook stack for a given key, from the top down.
	 *
	 * The `$argument` is passed to the first hook function, then the
	 * return value of that is passed to the second hook function, and
	 * so on, until the end of the stack is reached, and the return value
	 * from the last hook on the stack is returned from this function.
	 *
	 * In the case where there are no hook functions registered for the given
	 * key, the `$argument` is returned straight away from this function.
	 *
	 * @param string[] $key Hook key
	 * @param mixed $argument Argument to pass to the hook functions
	 * @return mixed Output from the last hook function
	 */
	public static function execute(array $key, $argument) {
		$mKey = self::canonicalize_hook_key($key);
		$hooks =
			array_key_exists($mKey, self::$hooks)
			? self::$hooks[$mKey]
			: [];

		foreach ($hooks as $hookIndex => $hook) {
			$argument = call_user_func($hook, $key, $argument);
		}

		return $argument;
	}
}
