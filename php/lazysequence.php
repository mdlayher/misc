<?php
// PHP Lazy Sequences, Matt Layher, 9/6/13
// Generates a sequence on the fly by evaluating an input function over a given range

class LazySequence
{
	// The function to be evaluated
	private $f;

	// Input function
	public function __construct($f)
	{
		$this->f = $f;
	}

	// Evaluate the function over a range, optionally with step interval
	public function evaluate($start, $end, $step = 1)
	{
		// Store function locally so we can evaluate it
		$f = $this->f;

		// Iterate the range, using stepping
		for ($i = $start; $i < $end; $i += $step)
		{
			// Add evaluted function at this point to the sequence
			yield $f($i);
		}
	}
}

// Generate a lazy sequence which computes the square function on input values
$square = new LazySequence(function($x)
{
	return $x * $x;
});

// Evaluate the sequence to display its usage
$i = 1;
foreach ($square->evaluate(1, 10) as $s)
{
	printf("%d * %d = %d\n", $i, $i, $s);
	$i++;
}
