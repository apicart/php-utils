<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Arrays;

use Apicart\Utils\Arrays\Arrays;
use PHPUnit\Framework\TestCase;

final class ArraysTest extends TestCase
{

	public function testMerge(): void
	{
		$one = [
			'a' => 1,
			'b' => [1, 2, 3],
			'c' => false,
		];
		$two = [
			'c' => true,
			'd' => ['a'],
		];

		$result = Arrays::merge($two, $one);

		self::assertSame([
			'a' => 1,
			'b' => [1, 2, 3],
			'c' => true,
			'd' => ['a'],
		], $result);
	}


	public function testGet(): void
	{
		$data = [
			'id' => 12,
			'tags' => ['first', 'second', 'third'],
			'parameters' => [
				[
					'key' => 'billingAddress',
					'value' => [
						'firstName' => 'John',
						'lastName' => 'Doe',
					],
				],
				[
					'key' => 'paymentMethod',
					'value' => 'PPL',
				],
			],
			'tree' => [
				'left' => [
					'node' => 'Left node',
				],
				'right' => [
					'node' => 'Right node',
				],
			],
		];

		self::assertSame(12, Arrays::get($data, 'id'));
		self::assertSame('second', Arrays::get($data, 'tags.1'));
		self::assertSame('John', Arrays::get($data, 'parameters.key:billingAddress.value.firstName'));
		self::assertSame('PPL', Arrays::get($data, 'parameters.key:paymentMethod.value'));
		self::assertSame('Right node', Arrays::get($data, 'tree.right.node'));

		self::assertSame(null, Arrays::get($data, 'tree.center.node'));
		self::assertSame('Default value', Arrays::get($data, 'tree.center.node', 'Default value'));
	}

}
