<?php
declare(strict_types=1);
namespace ix\Helpers;

class ArrayHelpers {
	/**
	 * Flatten an array.
	 *
	 * @param mixed[] $data
	 * @param mixed[] $result
	 * @return mixed[]
	 */
	public static function array_flatten($data, array $result = []): array {
		foreach ($data as $flat) {
			if (is_array($flat)) {
				$result = self::array_flatten($flat, $result);
			} else {
				$result[] = $flat;
			}
		}

		return $result;
	}
}
