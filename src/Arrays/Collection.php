<?php declare(strict_types = 1);

namespace Apicart\Utils\Arrays;

use ArrayAccess;
use Countable;
use Iterator;
use IteratorAggregate;
use RecursiveArrayIterator;

final class Collection implements ArrayAccess, Countable, IteratorAggregate
{

	/**
	 * @var callable
	 */
	private $callable;

	/**
	 * @var array
	 */
	private $loadedData = null;


	public function __construct(callable $dataProvider)
	{
		$this->callable = $dataProvider;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getIterator(): Iterator
	{
		$this->initialize();

		return new RecursiveArrayIterator($this->loadedData);
	}


	/**
	 * @param mixed $offset
	 */
	public function offsetExists($offset): bool
	{
		$this->initialize();

		return isset($this->loadedData[$offset]);
	}


	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		$this->initialize();

		return $this->loadedData[$offset];
	}


	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value): void
	{
		$this->initialize();

		$this->loadedData[$offset] = $value;
	}


	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset): void
	{
		$this->initialize();

		unset($this->loadedData[$offset]);
	}


	/**
	 * {@inheritdoc}
	 */
	public function count(): int
	{
		$this->initialize();

		return count($this->loadedData);
	}


	public function toArray(): array
	{
		$this->initialize();

		return $this->loadedData;
	}


	private function initialize(): void
	{
		if ($this->loadedData === null) {
			$data = call_user_func($this->callable);
			$this->loadedData = is_array($data) ? $data : [$data];
		}
	}

}
