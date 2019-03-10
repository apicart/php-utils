<?php declare(strict_types = 1);

namespace Apicart\Utils\Arrays;

use InvalidArgumentException;

final class Arrays
{

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get(array $array, string $path, $default = null)
	{
		$return = $default;
		$keyParts = explode('.', $path);
		$rootKey = (string) array_shift($keyParts);
		if (strpos($rootKey, ':') !== false) { // parse sub-structure search
			$parts = explode(':', $rootKey);
			$rootKey = $parts[0];
			$rootValue = $parts[1];
		}
		foreach ($array as $mainIndex => $mainValue) {
			if (isset($rootValue)) {
				if (is_array($mainValue)) {
					foreach ($mainValue as $key => $value) {
						if ($key === $rootKey && $value === $rootValue) {
							if (count($keyParts) > 0) {
								$return = self::get($mainValue, implode('.', $keyParts), $default);
							} else {
								$return = $mainValue;
							}
							break 2;
						}
					}
				}
				continue;
			}

			if ($mainIndex === $rootKey || (is_int($mainIndex) && $mainIndex === (int) $rootKey)) {
				if (is_array($mainValue) && $keyParts !== []) {
					$return = self::get($mainValue, implode('.', $keyParts), $default);
				} elseif ($keyParts !== []) {
					$return = $default;
				} else {
					$return = $mainValue;
				}

				break;
			}
		}

		return $return;
	}


	/**
	 * @param string|int|array $key one or more keys
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public static function &getReference(array &$array, $key, string $keySeparator = '.')
	{
		if (is_string($key)) {
			$key = (array) explode($keySeparator, $key);
		} elseif (! is_array($key)) {
			$key = [$key];
		}

		foreach ($key as $index) {
			if (is_array($array) || $array === null) {
				$array = &$array[$index];
			} else {
				throw new InvalidArgumentException('Traversed item is not an array.');
			}
		}

		return $array;
	}


	/**
	 * @param string|array $key
	 * @param mixed $value
	 */
	public static function set(array $haystack, $key, $value, string $keySeparator = '.'): array
	{
		if (is_string($key)) {
			$key = explode($keySeparator, $key);
		} elseif (! is_array($key)) {
			$key = [$key];
		}

		$clone = $haystack;
		$valueRef = & self::getReference($clone, $key);
		$valueRef = $value;

		return $clone;
	}


	/**
	 * @param array|string|null $left
	 * @param array|string|null $right
	 * @return array
	 */
	public static function merge($left, $right)
	{
		if (is_array($left) && is_array($right)) {
			foreach ($left as $key => $val) {
				if (is_int($key)) {
					$right[] = $val;
				} else {
					if (isset($right[$key])) {
						$val = static::merge($val, $right[$key]);
					}
					$right[$key] = $val;
				}
			}
			return $right;
		} elseif ($left === null && is_array($right)) {
			return $right;
		}

		return $left;
	}

}
