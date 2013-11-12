<?php
// A basic example to showcase the usefulness of immutable state and state transformation, as
// is used in functional programming

class RGB
{
	protected $red;
	protected $green;
	protected $blue;

	public function __construct($red = 0, $green = 0, $blue = 0)
	{
		// Ensure all values within valid range
		if (!is_int($red) || $red < 0 || $red > 255)
		{
			throw new InvalidArgumentException("Value for red must be an integer between 0 and 255");
		}
		if (!is_int($green) || $green < 0 || $green > 255)
		{
			throw new InvalidArgumentException("Value for green must be an integer between 0 and 255");
		}
		if (!is_int($blue) || $blue < 0 || $blue > 255)
		{
			throw new InvalidArgumentException("Value for blue must be an integer between 0 and 255");
		}

		$this->red = $red;
		$this->green = $green;
		$this->blue = $blue;
	}

	// Convert to hex for display
	public function __toString()
	{
		return $this->toHex();
	}

	// Set red value, transform state
	public function red($red = 255)
	{
		return new self($red, $this->green, $this->blue);
	}

	// Set green value, transform state
	public function green($green = 255)
	{
		return new self($this->red, $green, $this->blue);
	}

	// Set blue value, transform state
	public function blue($blue = 255)
	{
		return new self($this->red, $this->green, $blue);
	}

	// Output hex value of RGB object
	public function toHex()
	{
		return strtoupper(sprintf("#%02x%02x%02x", $this->red, $this->green, $this->blue));
	}
}

// Create RGB palette
$rgb = new RGB(0, 0, 0);

// Create new state by transforming previous state
$red = $rgb->red();
$white = $rgb->red()->green()->blue();
$blue = $rgb->blue();

// New state created by transformations
printf("  red: %s\n", $red);
printf("white: %s\n", $white);
printf(" blue: %s\n", $blue);

// Operations called on RGB are idempotent, and never modify its original value
printf("  rgb: %s\n", $rgb);
