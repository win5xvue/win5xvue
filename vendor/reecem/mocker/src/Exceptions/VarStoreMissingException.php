<?php

namespace ReeceM\Mocker\Exceptions;

use Exception;
use Throwable;

class VarStoreMissingException extends Exception
{
	/**
	 * THrow an error for when the VarStore class is not passed in the constructor
	 * @param string $message
	 * @param int $code
	 * @param null|Throwable $previous
	 * @return mixed
	 */
	public function __construct($message = "Singleton Storage Class not passed in constructor", $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
