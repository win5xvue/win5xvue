<?php

namespace ReeceM\Mocker\Traits;

use Illuminate\Support\Arr;

/**
 * Contains the magic methods for object related calls
 *
 * abstracted to a trait to maintain readability and maintainability
 */

trait ObjectMagic
{

	/**
	 * Get an object from the mocked class, this returns a new instance of mocked
	 *
	 */
	public function __get($name)
	{
		/**
		 * @todo maybe return the value of the variable if it has been set and has a value
		 *  and the call has some math after it
		 */

		return new self(debug_backtrace(false, 1), $this->store, $this->trace);
	}

	/**
	 * Set a method to the calls
	 */
	public function __call($name, $arguments)
	{
		// return new self()
	}

	/**
	 * set the value of something inside the class
	 */
	public function __set($name, $value)
	{
		return new self(debug_backtrace(false, 1), $this->store, $this->trace);
	}
}
