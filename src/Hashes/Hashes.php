<?php declare(strict_types = 1);

namespace Apicart\Utils\Hashes;

use InvalidArgumentException;

final class Hashes
{

	/**
	 * @throws InvalidArgumentException
	 */
	public static function generate(int $length = 32, string $charList = '0-9a-z'): string
	{
		$charList = count_chars(preg_replace_callback('#.-.#', function (array $matches): string {
			return implode('', range($matches[0][0], $matches[0][2]));
		}, $charList), 3);

		$charListLength = strlen($charList);
		if ($length < 1) {
			throw new InvalidArgumentException('Length must be greater than zero.');
		} elseif ($charListLength < 2) {
			throw new InvalidArgumentException('Character list must contain at least two chars.');
		}

		$hash = '';
		for ($i = 0; $i < $length; $i++) {
			$hash .= $charList[random_int(0, $charListLength - 1)];
		}

		return $hash;
	}

}
