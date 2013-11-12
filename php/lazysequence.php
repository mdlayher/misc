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

// Generate a lazy fibonacci sequence
$fibonacci = new LazySequence(function($x)
{
	// By definition
	if ($x == 0)
	{
		return 0;
	}
	else if ($x == 1 || $x == 2)
	{
		return 1;
	}

	// Initial values
	$a = 1;
	$b = 1;

	// Output of sequence
	$fib = 0;

	for ($i = 0; $i <= $x - 2; $i++)
	{
		$fib = $a + $b;
		$b = $a;
		$a = $fib;
	}

	return $fib;
});

// Evaluate lazy fibonacci sequence
foreach ($fibonacci->evaluate(1, 10) as $f)
{
	echo "$f, ";
}
echo "...";
