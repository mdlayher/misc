<?php
// Examples adapted from: http://blog.ircmaxell.com/2013/11/beyond-object-oriented-programming.html

// Summing an array in different programming paradigms

// Procedural, with state
function sum_array_procedural(array $array)
{
	$sum = 0;
	foreach ($array as $value)
	{
		$sum += $value;
	}

	return $sum;
}

// Object-Oriented, make array sum itself
class ObjArray
{
	private $array;

	public function __construct(array $array)
	{
		$this->array = $array;
	}

	public function sum_oop()
	{
		$sum = 0;
		foreach ($this->array as $value)
		{
			$sum += $value;
		}

		return $sum;
	}
}

// Functional, series of transformations with no state involved
function sum_array_functional(array $array)
{
	// Ensure first element defined
	if (isset($array[0]))
	{
		// Sum first element, slice array to remove first element and recursively sum
		return $array[0] + sum_array_functional(array_slice($array, 1));
	}

	// If no value set, return 0
	return 0;
}

$array = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
$obj = new ObjArray($array);

// Sums are all equal
printf("procedural: %d\n", sum_array_procedural($array));
printf("    object: %d\n", $obj->sum_oop());
printf("functional: %d\n", sum_array_functional($array));
