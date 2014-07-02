// main defines the program entry point
fn main() {
	// println!() is a macro which functions similar to C printf()
	println!("hello, {}!\n", "world");

	// All variables are immutable unless declared with "let mut"
	let item = "apple";

	// Declaring price using conditional statements
	let price: f32 =
		if item == "apple" {
			1.00
		} else if item == "banana" {
			1.50
		} else {
			2.00
		};

	println!("The price of {} is {}", item, price);

	// Calling and using function return
	println!("signum({}) = {}\n", -10i, signum(-10));

	// The "match" statement is a more generalized version of C "switch", which accepts expressions
	let num = 1i;
	match num {
		// Single condition
		0     => { println!("zero") }
		// One or the other
		1 | 2 => { println!("one or two") }
		// Range of conditions
		3..10 => { println!("three to ten") }
		// match is exhaustive, so a wildcard case is required at compile-time
		_     => { println!("something else") }
	}
}

// signum returns the sign of an integer
fn signum(x: int) -> int {
	if x < 0 { -1 }
	else if x > 0 { 1 }
	else { 0 }
}
