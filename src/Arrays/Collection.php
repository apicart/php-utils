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


	public function createFrom(callable $dataProvider): self
	{
		return new static($dataProvider);
	}


	/**
	 * @return mixed|null
	 */
	public function column(int $index = 0)
	{
		$firstRow = $this->first();
		if (is_array($firstRow) === true) {
			$values = array_values($firstRow);

			return $values[$index] ?? null;
		}

		return null;
	}


	/**
	 * @return mixed|bool
	 */
	public function first()
	{
		$this->initialize();

		return reset($this->loadedData);
	}


	/**
	 * @return mixed|bool
	 */
	public function last()
	{
		$this->initialize();

		return end($this->loadedData);
	}


	/**
	 * @return int|string|null
	 */
	public function key()
	{
		$this->initialize();

		return key($this->loadedData);
	}


	/**
	 * @return mixed|bool
	 */
	public function next()
	{
		$this->initialize();

		return next($this->loadedData);
	}


	/**
	 * @return mixed|bool
	 */
	public function current()
	{
		$this->initialize();

		return current($this->loadedData);
	}


	/**
	 * @param int|string $key
	 * @return mixed|null
	 */
	public function remove($key)
	{
		$this->initialize();

		if (! isset($this->loadedData[$key]) && ! $this->contains($key)) {
			return null;
		}

		$removed = $this->loadedData[$key];
		unset($this->loadedData[$key]);

		return $removed;
	}


	/**
	 * @param mixed $element
	 * @return bool|int|string
	 */
	public function indexOf($element)
	{
		return array_search($element, $this->toArray(), true);
	}


	/**
	 * @param int|string $key
	 * @return mixed|null
	 */
	public function get($key)
	{
		$this->initialize();

		return $this->loadedData[$key] ?? null;
	}


	public function getKeys(): array
	{
		$this->initialize();

		return array_keys($this->loadedData);
	}


	public function getValues(): array
	{
		$this->initialize();

		return array_values($this->loadedData);
	}


	/**
	 * @param int|string $key
	 * @param mixed $value
	 */
	public function set($key, $value): void
	{
		$this->initialize();

		$this->loadedData[$key] = $value;
	}


	/**
	 * @param mixed $element
	 */
	public function add($element): bool
	{
		$this->initialize();

		$this->loadedData[] = $element;

		return true;
	}


	public function isEmpty(): bool
	{
		return $this->toArray() === [];
	}


	public function clear(): void
	{
		$this->initialize();

		$this->loadedData = [];
	}


	/**
	 * {@inheritdoc}
	 */
	public function getIterator(): Iterator
	{
		return new RecursiveArrayIterator($this->toArray());
	}


	/**
	 * @param mixed $offset
	 */
	public function offsetExists($offset): bool
	{
		return $this->contains($offset);
	}


	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}


	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value): void
	{
		$this->set($offset, $value);
	}


	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset): void
	{
		$this->remove($offset);
	}


	/**
	 * {@inheritdoc}
	 */
	public function count(): int
	{
		return count($this->toArray());
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


	/**
	 * @param int|string $key
	 */
	private function contains($key): bool
	{
		$this->initialize();

		return isset($this->loadedData[$key]) || array_key_exists($key, $this->loadedData);
	}

}
