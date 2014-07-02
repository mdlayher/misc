use std::f64;

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

	// Declare a tuple
	let tup = (0.0f64, 1.0f64);
	// Compute angle using tuple
	let ang = angle(tup);
	println!("\nangle({}) = {}\n", tup, ang);

	// Bind subpattern to variable
	match 18i {
		age @ 0..20 => println!("{} years old", age),
		_ => println!("some other age")
	}

	// Destructure tuple using let
	let (x, y) = tup;
	println!("\n(x:{}, y:{}) == tup:{}\n", x, y, tup);

	// while loop
	let mut amount = 8i;
	while amount > 0 {
		amount -= 1;
		println!("cake: {}", amount);
	}

	// infinite loop
	loop {
		// ... not so much
		println!("\nbreak!\n");
		break;
	}

	// for loop
	for i in range(0u, 5) {
		println!("count: {}", i);
	}
}

// signum returns the sign of an integer
fn signum(x: int) -> int {
	if x < 0 { -1 }
	else if x > 0 { 1 }
	else { 0 }
}

// Using pattern matching for "destructuring": matching to bind names to the contents of data types
// A tuple is declared as follows
fn angle(vector: (f64, f64)) -> f64 {
	let pi = f64::consts::PI;
	match vector {
		// A variable name matches any value, AND binds the value to the arm's action
		// Matches any tuple whose first element is zero, AND binds y to the second element
		(0.0, y) if y < 0.0 => 1.5 * pi,
		// Matches any tuple whose first element is zero, but does not bind anything to the second element
		(0.0, _) => 0.5 * pi,
		// Matches any tuple, and binds both elements to variables
		(x, y) => (y / x).atan()
	}
}
