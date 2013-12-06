// Here we GO!
// Thanks: http://learnxinyminutes.com/docs/go/

// Use package "main" to denote an executable, rather than a library
package main

// Reference libraries using import
import (
	"fmt" // Text formatting
	"net/http"	// Basic web server
	"strconv"	// String conversion
)

// Main is the entry point
func main() {
	// Basic line printing
	fmt.Println("Hello world!")

	// Call functions
	beyondHello()
	learnTypes()
	learnFlowControl()
	learnInterfaces()
	learnErrorHandling()
	learnConcurrency()
	learnWebProgramming()
}

// Another function
func beyondHello() {
	// Declare and initialize variable, long way
	var x int
	x = 3

	// Short method, automatic declare and infers type
	y := 4

	// Call a function which returns two values
	sum, prod := learnMultiple(x, y)
	fmt.Println("sum:", sum, "prod:", prod)
}

func learnMultiple(x, y int) (sum, prod int) {
	// Return two values
	return x + y, x * y
}

func learnTypes() {
	// Arrays: fixed size at compile time
	// Array of 4 integers
	var a4 [4]int
	// Array of 3 integers, initialized as shown
	a3 := [...]int{3, 1, 5}

	// Slices: dynamic size
	// Slice of 3 ints, no ellipsis
	s3 := []int{4, 5, 9}
	// Allocate slice of 4 ints, all 0
	s4 := make([]int, 4)
	// Turn string into byte slice
	bs := []byte("a slice")

	// Maps: dynamically growable associative array type
	m := map[string]int{
		"three": 3,
		"four":  4,
	}
	m["one"] = 1

	// Unused variables are an error, so we can use underscore to discard them
	_, _ = a4, s4

	// Output variables
	fmt.Println("a3:", a3, "s3:", s3, "bs:", bs, "m:", m)
}

func learnFlowControl() {
	// If statement
	if true {
		fmt.Println("true")
	} else {
		fmt.Println("false")
	}

	// Switch, cases don't fall through
	x := 1
	switch x {
		case 0:
		case 1:
			fmt.Println("ONE")
		case 2:
			// not used
	}

	// For: only loop statement in Go
	for x := 0; x < 3; x++ {
		fmt.Println("x:", x)
	}

	// Infinite loop
	for {
		// ... not really
		break
	}

	// Declare and assign y, then test
	if y := someComputation(); y > x {
		x = y
	}

	// Function literals are closures
	xBig := func() bool {
		// x automatically "enclosed" by closure
		return x > 100
	}

	fmt.Println("xBig:", xBig())
}

func someComputation() int {
	return 17
}

// Define interface
type Stringer interface {
	String() string
}

// Define a struct with two int fields
type pair struct {
	x, y int
}

// Define a method on pair, so pair now implements Stringer
func (p pair) String() string {
	return fmt.Sprintf("(%d, %d)", p.x, p.y)
}

func learnInterfaces() {
	// Braces: struct literal, evalutes to initialized struct
	p := pair{3, 4}
	fmt.Println(p.String())

	// Create instance of Stringer
	var i Stringer
	// Valid because pair implements Stringer
	i = p
	fmt.Println(i.String())

	// Create String method to implicitly print a struct
	fmt.Println("toString:", p)
}

func learnErrorHandling() {
	// ", ok" is an idiom to tell if something worked
	m := map[int]string{3: "three", 4: "four"}
	if x, ok := m[1]; !ok {
		fmt.Println("not found!")
	} else {
		fmt.Print(x)
	}

	// err is like an exception
	// Discard return value
	if _, err := strconv.Atoi("non-int"); err != nil {
		fmt.Println(err)
	}
}

// Increment x, send value to channel
func inc(x int, c chan int) {
	x++
	c <- x
}

func learnConcurrency() {
	// Make can allocate slices, maps, and channels
	// c is a channel
	c := make(chan int)

	// Start three concurrent goroutines, all incrementing to the same channel
	go inc(0, c)
	go inc(10, c)
	go inc(-805, c)

	// Read three results from channel and print
	// Result order cannot be predicted
	// <- is the "receive" operator
	fmt.Println("one:", <-c, "two:", <-c, "three:", <-c)

	// Create string channel, and channel of string channels
	cs := make(chan string)
	cc := make(chan chan string)

	// Anonymous goroutines
	go func() { c <- 84 }()
	go func() { cs <- "word" }()

	// Select is like a switch, but each case involves a channel operation
	// It will select a case at random out of cases that are ready to communicate
	// Select statement will run one time through only!
	select {
		// i can be assigned to value from int channel
		case i := <-c:
			fmt.Printf("it's a %T", i)
		// Value received, but can be discarded
		case <-cs:
			fmt.Println("Got a string!")
		// Empty channel, not ready for communication
		case <-cc:
			fmt.Println("This won't happen!")
	}
}

func learnWebProgramming() {
	// ListenAndServe HTTP server on specified port, second parameter is an interface http.Handler
	err := http.ListenAndServe(":8080", pair{})
	fmt.Println(err)
}

// Make pair a http.Handler by implementing its method
func (p pair) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	w.Write([]byte("You learned Go!"))
}
