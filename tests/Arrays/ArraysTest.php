<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Arrays;

use Apicart\Utils\Arrays\Arrays;
use PHPUnit\Framework\TestCase;

final class ArraysTest extends TestCase
{

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


	public function testGetReference(): void
	{
		$data = [
			'id' => 12,
			'tree' => [
				'left' => [
					'node' => 'Left node',
				],
			],
		];

		$nodeReference = &$data['tree']['left']['node'];
		self::assertSame($nodeReference, Arrays::getReference($data, ['tree', 'left', 'node']));

		$idReference = & Arrays::getReference($data, 'id');
		$idReference = 10;
		self::assertSame($idReference, $data['id']);
	}


	public function testSet(): void
	{
		$data = [
			'id' => 12,
			'tree' => [
				'left' => [
					'node' => 'Left node',
				],
			],
		];

		$result = Arrays::set($data, 'id', 1);
		self::assertSame(1, $result['id']);

		$result = Arrays::set($data, 'tree.left.position', 3);
		self::assertSame(3, $result['tree']['left']['position']);

		$result = Arrays::set($data, 'foo', 'bar');
		self::assertSame('bar', $result['foo']);
	}


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

}
