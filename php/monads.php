<?php

// Derived from: http://blog.ircmaxell.com/2013/07/taking-monads-to-oop-php.html

abstract class AbstractMonad
{
	// Value contained by monad
	protected $value;

	// Create a monad and store value
	public function __construct($value)
	{
		$this->value = $value;
	}

	// Bind a callback function with arguments to monad
	public function bind(callable $function, array $args = array())
	{
		return self::unit($this->callback($function, $args));
	}

	// Extract value from monad
	public function extract()
	{
		// If value is an instance of monad, extract value from that monad
		if ($this->value instanceof self)
		{
			return $this->value->extract();
		}

		return $this->value;
	}

	// Create a static instance of value if it isn't already static
	public static function unit($value)
	{
		if ($value instanceof static)
		{
			return $value;
		}

		return new static($value);
	}

	// Perform callback function with arguments
	protected function callback($function, array $args = array())
	{
		// If value is a monad, bind function to it
		if ($this->value instanceof self)
		{
			return $this->value->bind($function, $args);
		}

		// Else, call function with arguments, prepending value to args
		array_unshift($args, $this->value);
		return call_user_func_array($function, $args);
	}
}

// Identity: the basic monad
class IdentityMonad extends AbstractMonad
{
}

$monad = IdentityMonad::unit(10);
$newMonad = $monad->bind(function($value)
{
	return $value / 2;
});

// Will print whatever the output of the function bound to monad performs
printf("IDENTITY: %d\n", $newMonad->extract());

// Maybe: abstracts away null values, by only calling callback if wrapped value isn't null
class MaybeMonad extends AbstractMonad
{
	// Override parent bind to only bind if non-null
	public function bind($function)
	{
		if (!is_null($this->value))
		{
			return parent::bind($function);
		}

		return $this::unit(null);
	}
}

$monad = MaybeMonad::unit(10);
$newMonad = $monad->bind(function($value)
{
	printf("MAYBE: %d\n", $value);
});

// Will only run bound function if value is not null
$newMonad->extract();
