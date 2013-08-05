<?php
	// All vehicles implement interface, implement getName function
	interface IVehicle
	{
		public function getName();
	}

	// Car is a IVehicle implementation
	class Car implements IVehicle
	{
		public function getName()
		{
			return "Car";
		}
	}

	// Boat is a IVehicle implementation
	class Boat implements IVehicle
	{
		public function getName()
		{
			return "Boat";
		}
	}

	// Plane is a IVehicle implementation
	class Plane implements IVehicle
	{
		public function getName()
		{
			return "Plane";
		}
	}

	// All in array are IVehicle implementations
	$vehicles = array(new Car(), new Boat(), new Plane());

	// ... so they can be processed using polymorphism!
	foreach ($vehicles as $v)
	{
		printf("vehicle: %s\n", $v->getName());
	}
