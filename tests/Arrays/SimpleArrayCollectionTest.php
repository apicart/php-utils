<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Arrays;

use Apicart\Utils\Arrays\SimpleArrayCollection;
use PHPUnit\Framework\TestCase;

final class SimpleArrayCollectionTest extends TestCase
{

	private const TEST_DATASET = [1, 'b', false];

	/**
	 * @var SimpleArrayCollection
	 */
	private $simpleArrayCollection;


	protected function setUp(): void
	{
		$this->simpleArrayCollection = new SimpleArrayCollection(self::TEST_DATASET);
	}


	public function testGetArray(): void
	{
		self::assertSame(self::TEST_DATASET, $this->simpleArrayCollection->toArray());
	}


	public function testColumn(): void
	{
		self::assertSame(null, $this->simpleArrayCollection->column());
	}


	public function testFirst(): void
	{
		self::assertSame(1, $this->simpleArrayCollection->first());
	}


	public function testLast(): void
	{
		self::assertSame(false, $this->simpleArrayCollection->last());
	}


	public function testGetIterator(): void
	{
		$iterator = $this->simpleArrayCollection->getIterator();
		foreach ($iterator as $key => $value) {
			self::assertSame(self::TEST_DATASET[$key], $value);
		}
	}


	public function testOffsetExists(): void
	{
		self::assertTrue($this->simpleArrayCollection->offsetExists(1));
		self::assertFalse($this->simpleArrayCollection->offsetExists(5));

		self::assertTrue(isset($this->simpleArrayCollection[1]));
		self::assertFalse(isset($this->simpleArrayCollection[5]));
	}


	public function testOffsetGet(): void
	{
		self::assertSame('b', $this->simpleArrayCollection->offsetGet(1));
		self::assertSame(1, $this->simpleArrayCollection[0]);
	}


	public function testOffsetSet(): void
	{
		$this->simpleArrayCollection->offsetSet('foo', 'bar');
		self::assertSame('bar', $this->simpleArrayCollection['foo']);

		$this->simpleArrayCollection['tag'] = 'new';
		self::assertSame('new', $this->simpleArrayCollection->offsetGet('tag'));
	}


	public function testOffsetUnset(): void
	{
		self::assertTrue(isset($this->simpleArrayCollection[2]));
		$this->simpleArrayCollection->offsetUnset(2);
		self::assertFalse(isset($this->simpleArrayCollection[2]));

		self::assertTrue(isset($this->simpleArrayCollection[0]));
		unset($this->simpleArrayCollection[0]);
		self::assertFalse(isset($this->simpleArrayCollection[0]));
	}


	public function testCount(): void
	{
		self::assertSame(count(self::TEST_DATASET), $this->simpleArrayCollection->count());
		self::assertSame(count(self::TEST_DATASET), count($this->simpleArrayCollection));
	}


	public function testToArray(): void
	{
		self::assertSame(self::TEST_DATASET, $this->simpleArrayCollection->toArray());
	}

}
