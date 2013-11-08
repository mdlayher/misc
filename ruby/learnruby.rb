# Thanks: http://learnxinyminutes.com/docs/ruby/

# Comment!

# Strings

name = "interpolation"
puts "Basic string #{name}"

printf("C-style printf: %d + %d = %d\n\n", 2, 2, 2 + 2)

# Symbols

# Symbols are kind of like enums, can be used instead of strings

status = :pending

printf("status: %s\n\n", status == :pending)

# Arrays
array = [1, 2, 3, 4, 5, "a", "b", "c"]

# Indexing
printf("array[0]: %s, array[-1]: %s\n", array[0], array[-1])

# Slicing to new array using start and end index
new = array[2, 4]
printf("new[-1]: %s\n", new[-1])

# Slicing to new array using range
new = array[1..3]
printf("new[1]: %s\n", new[1])

# Adding item to array
array << "d"
printf("array[-1]: %s\n\n", array[-1])

# Hashes, like PHP's associative arrays

hash = {
	"color" => "green",
	"number" => 5
}

# Array of keys
printf("key[0]: %s\n", hash.keys[0])

# Values
printf("hash['color']: %s, hash[\'number\']: %d\n", hash['color'], hash['number'])

# Symbols as keys
new_hash = {
	one: 1,
	two: 2
}

printf("new_hash[:one]: %s\n\n", new_hash[:one])

# Control structures

# if/elsif/else
if true
	puts "yes!"
elsif false
	puts "no!"
else
	puts "...maybe?"
end

# For loop over range
for c in 1..5
	printf("%d ", c)
end
puts "\n"

# Much more common in Ruby: each loop with block
(1..5).each do |c|
	printf("%d " , c)
end
puts "\n"

# One-liner
(1..5).each { |c| printf("%d ", c) }
puts "\n\n"

# Iterating data structures

# Array
puts "array:"
array = [1, 2, 3, 4, 5]
array.each do |e|
	puts e
end
puts "\n"

# Hash
puts "hash:"
hash = {
	"one" => 1,
	"two" => 2,
	"three" => 3,
}
hash.each do |k, v|
	printf("%s: %d\n", k, v)
end
puts "\n"

# Switch statement
grade = 'B'
case grade
when 'A'
	puts "Great!"
when 'B'
	puts "Good!"
when 'C'
	puts "OK"
else
	puts "Boo!"
end
puts "\n"

# Functions

def double(x)
	# Functions implicitly return value of last statement
	x * 2
end

# Parantheses optional
puts "double:"
puts double(2)
puts double 2
puts double double 2
puts "\n"

# Multiple parameters
def sum(x, y)
	x + y
end

puts "sum:"
puts sum 3, 4
puts sum sum(3, 4), 5
puts "\n"

# yield: all methods have an implicit, optional black parameter, called by yield keyword
def surround
	puts "{"
	yield
	puts "}"
end

# Block is injected into code
surround { puts "hello world" }

# Can pass a black to a function, & marks reference to block
def guests(&block)
	block.call "args"
end

# Variadic parameters
def guests(*array)
	array.each { |guest| puts "hello, #{guest}" }
end
puts "\n"

# Classes

class Human
	# Class variable ("static"), shared by all instances
	@@species = "H. sapiens"

	# Initializer ("constructor"), with default parameter
	def initialize(name, age = 0)
		# Assign to instance variable
		@name = name
		@age = age
	end

	# Getter/setters
	def name=(name)
		@name = name
	end

	def name
		# Implicit return
		@name
	end

	def age
		@age
	end

	# Class method ("static"), only called by class, not instance
	def self.say(msg)
		puts msg
	end

	def species
		@@species
	end
end

# Create human
matt = Human.new("Matt Layher", 22)
phil = Human.new("Phil Roth")

# Use instance methods
printf("%s: species: %s, age, %d\n", matt.name, matt.species, matt.age)

# Call class method
Human.say("Hello world!")

# Scope

$var = "global"
@var = "instance"
@@var = "class"
Var = "constant"

# Derived class
# Retains class variables, but not instance variables
class Worker < Human
end

Worker.say("Hello, I'm a worker")
puts "\n"

# Modules, similar to traits

module ModuleExample
	def foo
		'foo'
	end
end

# Include -> bind methods to object instance
class Person
	include ModuleExample
end

# Extend -> bind methods to class instance
class Book
	extend ModuleExample
end

# Object instance
puts Person.new.foo
# Class instance
puts Book.foo
