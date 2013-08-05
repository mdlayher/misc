<?php
	// Chain of Responsibilities design pattern demo
	// Matt Layher, 8/2/13

	interface KeyValueStorage
	{
		public function dump();
		public function get($key);
		public function set($key, $value);
	}

	// Slow storage, for example: a database
	class DiskStorage implements KeyValueStorage
	{
		protected $data = array();

		public function __construct()
		{
		}

		// Return disk storage data
		public function dump()
		{
			return $this->data;
		}

		// Try to retrieve data, return null if key not found
		public function get($key)
		{
			printf("disk get(): %s\n", $key);
			return isset($this->data[$key]) ? $this->data[$key] : null;
		}

		// Set data into array, overwriting old value with same key
		public function set($key, $value)
		{
			printf("disk set(): %s -> %s\n", $key, $value);
			$this->data = array_merge($this->data, array($key => $value));
			return true;
		}
	}

	// Fast storage, for example: memcached or redis
	class MemoryStorage implements KeyValueStorage
	{
		protected $data = array();
		protected $handler;

		// Set the "next" handler in the chain, in case this one can't find data
		public function __construct(KeyValueStorage $handler)
		{
			$this->handler = $handler;
		}

		// Merge all data with this object's array with the data from next in chain
		public function dump()
		{
			return array_merge($this->data, $this->handler->dump());
		}

		// Try to retrieve data, ask next handler for data if not found
		public function get($key)
		{
			printf("mem get(): %s\n", $key);
			return isset($this->data[$key]) ? $this->data[$key] : $this->handler->get($key);
		}

		// Set data, tell next handler to do the same
		public function set($key, $value)
		{
			printf("mem set(): %s -> %s\n", $key, $value);
			$this->data = array_merge($this->data, array($key => $value));
			return $this->handler->set($key, $value);
		}
	}

	// "Fake" bloom filter, which can be used to determine membership in storage
	class BloomFilter
	{
		// A pseudo "filter", which would store values the filter is aware of
		protected $filter = array();

		// Set next handler in chain
		public function __construct(KeyValueStorage $handler)
		{
			$this->handler = $handler;
		}

		// Dump data from any actual data handlers
		public function dump()
		{
			return $this->handler->dump();
		}

		// Dump JSON instead of array
		public function dumpJson()
		{
			return json_encode($this->dump());
		}

		// Checks filter for membership, passes data on if found
		public function get($key)
		{
			// Bloom filter says "definitely not in set", so return null
			// This is useful to avoid having to hit a data store for data which won't ever exist
			if (!isset($this->filter[sha1($key)]))
			{
				printf("bloom: %s -> no\n", $key);
				return null;
			}

			// Bloom filter says "probably in set", so check down the chain
			printf("bloom: %s -> probably\n", $key);
			return $this->handler->get($key);
		}

		// Make the filter aware of this data, actually store it down the chain
		public function set($key, $value)
		{
			printf("bloom: add %s\n", $key);
			$this->filter = array_merge($this->filter, array(sha1($key) => $value));
			return $this->handler->set($key, $value);
		}
	}

	// This is where this pattern shows its true power
	$storage = new BloomFilter(new MemoryStorage(new DiskStorage()));

	// Example data
	$storage->set("test", "mattlayher");
	$storage->get("test");

	$storage->set("foo", "bar");
	$storage->get("baz");

	// Dump contents
	print_r($storage->dumpJson());
