<?php declare(strict_types = 1);

namespace Apicart\Utils\Arrays;

use ArrayAccess;
use Countable;
use Iterator;
use IteratorAggregate;
use RecursiveArrayIterator;

final class SimpleArrayCollection implements ArrayAccess, Countable, IteratorAggregate
{

	/**
	 * @var array
	 */
	private $elements = [];


	public function __construct(array $elements = [])
	{
		$this->elements = $elements;
	}


	public function createFrom(array $elements): self
	{
		return new static($elements);
	}


	public function toArray(): array
	{
		return $this->elements;
	}


	/**
	 * @return mixed|null
	 */
	public function column(int $index = 0)
	{
		$firstRow = $this->first();
		if ($firstRow !== null) {
			$values = array_values($firstRow);

			return $values[$index] ?? null;
		}

		return null;
	}


	/**
	 * @return mixed|null
	 */
	public function first()
	{
		$first = reset($this->elements);

		return $first ?: null;
	}


	/**
	 * @return mixed|null
	 */
	public function last()
	{
		$last = end($this->elements);

		return $last ?: null;
	}


	/**
	 * @return int|string|null
	 */
	public function key()
	{
		return key($this->elements);
	}


	/**
	 * @return mixed|null
	 */
	public function next()
	{
		$next = next($this->elements);

		return $next ?: null;
	}


	/**
	 * @return mixed|null
	 */
	public function current()
	{
		$current = current($this->elements);

		return $current ?: null;
	}


	/**
	 * @param int|string $key
	 * @return mixed|null
	 */
	public function remove($key)
	{
		if (! isset($this->elements[$key]) && ! $this->contains($key)) {
			return null;
		}

		$removed = $this->elements[$key];
		unset($this->elements[$key]);

		return $removed;
	}


	/**
	 * @param mixed $element
	 * @return bool|int|string
	 */
	public function indexOf($element)
	{
		return array_search($element, $this->elements, true);
	}


	/**
	 * @param int|string $key
	 * @return mixed|null
	 */
	public function get($key)
	{
		return $this->elements[$key] ?? null;
	}


	public function getKeys(): array
	{
		return array_keys($this->elements);
	}


	public function getValues(): array
	{
		return array_values($this->elements);
	}


	/**
	 * {@inheritdoc}
	 */
	public function count(): int
	{
		return count($this->elements);
	}


	/**
	 * @param int|string $key
	 * @param mixed $value
	 */
	public function set($key, $value): void
	{
		$this->elements[$key] = $value;
	}


	/**
	 * @param mixed $element
	 */
	public function add($element): bool
	{
		$this->elements[] = $element;

		return true;
	}


	public function isEmpty(): bool
	{
		return $this->elements === [];
	}


	public function clear(): void
	{
		$this->elements = [];
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
	 * @param int|string $key
	 */
	private function contains($key): bool
	{
		return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
	}

}
