<?php
	interface IVehicle
	{
		public function getName();
	}

	class Car implements IVehicle
	{
		public function getName()
		{
			return "Car";
		}
	}

	class Boat implements IVehicle
	{
		public function getName()
		{
			return "Boat";
		}
	}

	class Plane implements IVehicle
	{
		public function getName()
		{
			return "Plane";
		}
	}

	$vehicles = array(new Car(), new Boat(), new Plane());

	foreach ($vehicles as $v)
	{
		printf("vehicle: %s\n", $v->getName());
	}
