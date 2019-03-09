<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Hashes;

use Apicart\Utils\Hashes\Hashes;
use PHPUnit\Framework\TestCase;

final class HashesTest extends TestCase
{

	public function testGenerate(): void
	{
		$hash = Hashes::generate();
		self::assertTrue(strlen($hash) === 32);
		self::assertTrue((bool) preg_match('#^[a-z0-9]{32}$#', $hash));

		$anotherHash = Hashes::generate();
		self::assertTrue(strlen($anotherHash) === 32);
		self::assertTrue((bool) preg_match('#^[a-z0-9]{32}$#', $anotherHash));
		self::assertNotSame($hash, $anotherHash);

		$customHash = Hashes::generate(10, 'A-Z');
		self::assertTrue(strlen($customHash) === 10);
		self::assertTrue((bool) preg_match('#^[A-Z]{10}$#', $customHash));
	}

}
