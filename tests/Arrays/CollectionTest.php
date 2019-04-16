<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Arrays;

use Apicart\Utils\Arrays\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{

	private const TEST_DATASET = [1, 'b', false];

	/**
	 * @var Collection
	 */
	private $collection;


	protected function setUp(): void
	{
		$this->collection = new Collection(function () {
			return self::TEST_DATASET;
		});
	}


	public function testGetArray(): void
	{
		self::assertSame(self::TEST_DATASET, $this->collection->toArray());
	}


	public function testColumn(): void
	{
		self::assertSame(null, $this->collection->column());
	}


	public function testFirst(): void
	{
		self::assertSame(1, $this->collection->first());
	}


	public function testLast(): void
	{
		self::assertSame(false, $this->collection->last());
	}


	public function testGetIterator(): void
	{
		$iterator = $this->collection->getIterator();
		foreach ($iterator as $key => $value) {
			self::assertSame(self::TEST_DATASET[$key], $value);
		}
	}


	public function testOffsetExists(): void
	{
		self::assertTrue($this->collection->offsetExists(1));
		self::assertFalse($this->collection->offsetExists(5));

		self::assertTrue(isset($this->collection[1]));
		self::assertFalse(isset($this->collection[5]));
	}


	public function testOffsetGet(): void
	{
		self::assertSame('b', $this->collection->offsetGet(1));
		self::assertSame(1, $this->collection[0]);
	}


	public function testOffsetSet(): void
	{
		$this->collection->offsetSet('foo', 'bar');
		self::assertSame('bar', $this->collection['foo']);

		$this->collection['tag'] = 'new';
		self::assertSame('new', $this->collection->offsetGet('tag'));
	}


	public function testOffsetUnset(): void
	{
		self::assertTrue(isset($this->collection[2]));
		$this->collection->offsetUnset(2);
		self::assertFalse(isset($this->collection[2]));

		self::assertTrue(isset($this->collection[0]));
		unset($this->collection[0]);
		self::assertFalse(isset($this->collection[0]));
	}


	public function testCount(): void
	{
		self::assertSame(count(self::TEST_DATASET), $this->collection->count());
		self::assertSame(count(self::TEST_DATASET), count($this->collection));
	}


	public function testToArray(): void
	{
		self::assertSame(self::TEST_DATASET, $this->collection->toArray());
	}

}
