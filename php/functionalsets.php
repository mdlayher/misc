<?php

class set
{
	// Applies function $set to $element to determine if $element is contained
	public function contains($set, $element)
	{
		return $set($element);
	}

	// Generates a function which inserts $element for comparison, and which is applied by another function
	public function singleton($element)
	{
		return function($other) use ($element)
		{
			return $element == $other;
		};
	}

	// Generates a function which inserts $setA and $setB for comparison, and determines if function which applies
	// this function contains value $other
	public function union($setA, $setB)
	{
		return function($other) use ($setA, $setB)
		{
			return self::contains($setA, $other) || self::contains($setB, $other);
		};
	}

	// Generates a function which inserts $setA and $setB for comparison, and determines if function which applies
	// this function has a parameter $other which matches both sets
	public function intersect($setA, $setB)
	{
		return function($other) use ($setA, $setB)
		{
			return self::contains($setA, $other) && self::contains($setB, $other);
		};
	}

	// Generates a function which inserts $setA and $setB for comparison, and determines if function which applies
	// this function has a paraemter $other which is contained in $setA, but not $setB
	public function difference($setA, $setB)
	{
		return function($other) use ($setA, $setB)
		{
			return self::contains($setA, $other) && !self::contains($setB, $other);
		};
	}

	// Generates a function which inserts $set and a function $condition with which to filter that set, returning
	// all values in which condition is true
	public function filter($set, $condition)
	{
		return function($other) use ($set, $condition)
		{
			if ($condition($other))
			{
				return self::contains($set, $other);
			}

			return false;
		};
	}
}

$s = set::contains(function($f)
{
	return true;
}, 100);

echo "  contains: " . $s;
echo "\n";

$s = set::singleton(1);

echo " singleton: " . set::contains($s, 1);
echo "\n";

$s = set::singleton(1);
$t = set::singleton(2);
$union = set::union($s, $t);

echo "     union: " . set::contains($union, 2);
echo "\n";

$s = set::singleton(1);
$t = set::singleton(1);
$intersect = set::intersect($s, $t);

echo " intersect: " . set::contains($intersect, 1);
echo "\n";

$s = set::singleton(1);
$t = set::singleton(2);
$difference = set::difference($s, $t);

echo "difference: " . set::contains($difference, 1);
echo "\n";

$s = set::singleton(1);
$filter = set::filter($s, function($f)
{
	return $f <= 1;
});

echo "    filter: " . set::contains($filter, 1);
echo "\n";
