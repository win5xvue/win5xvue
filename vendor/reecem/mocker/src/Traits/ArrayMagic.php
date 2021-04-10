<?php

namespace ReeceM\Mocker\Traits;

use Illuminate\Support\Arr;

trait ArrayMagic
{

	/**
	 * Returns the iterator for loops
	 * @return \ArrayIterator
	 */
	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->store->memoized);
	}

	/**
	 * sets a value in the array object using the offset
	 * @param mixed $offset key of the array
	 * @param mixed $value the value to insert at offset
	 * @return void|self
	 */
	public function offsetSet($offset, $value)
	{
		return new self($this->arrayTrace(), $this->store, $this->trace);
	}

	/**
	 * This gets the value at the offset, if it is not set it will set and return a new instance
	 *
	 * @param mixed $offset the offset to get
	 * @return self
	 */
	public function offsetGet($offset)
	{
		return Arr::has($this->store->memoized, implode('.', $this->trace) . '.' . $offset)
			? Arr::get($this->store->memoized, implode('.', $this->trace) . '.' . $offset)
			: new self($this->arrayTrace(), $this->store, $this->trace);
	}

	/**
	 * checks if the key exists in the array
	 *  @return bool
	 */
	public function offsetExists($offset)
	{
		return Arr::has($this->store->memoized, implode('.', $this->trace));
	}
	/**
	 * unsets a key in array/object
	 *  @return void
	 */
	public function offsetUnset($offset)
	{
		Arr::pull($this->store->memoized, implode('.', $this->trace));
	}

	/**
	 * Back trace the calls in the array as we are getting it through two functions.
	 *
	 * @param int $depth how far back to go
	 * @return array
	 */
	private function arrayTrace($depth = 2): array
	{
		$selfTrace = debug_backtrace(false, $depth);
		$target = ($depth - 1) >= 0 ? $depth - 1 : 0;

		return array([
			'function'  => $selfTrace[$target]['function'] == 'offsetGet' ? '__get' : '__set',
			'args'      => $selfTrace[$target]['args'] ?? [null],
			'type'      => $selfTrace[$target]['type'] ?? '->',
			'__linking' => 'arr_mag',
		]);
	}
}
