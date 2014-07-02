// main defines the program entry point
fn main() {
	// println!() is a macro which functions similar to C printf()
	println!("hello, {}!\n", "world");

	let item = "apple";
	let price: f32 =
		if item == "apple" {
			1.00
		} else if item == "banana" {
			1.50
		} else {
			2.00
		};

	println!("The price of {} is {}", item, price);
}
