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

		public function dump()
		{
			return $this->data;
		}

		public function get($key)
		{
			printf("disk get(): %s\n", $key);
			return isset($this->data[$key]) ? $this->data[$key] : null;
		}

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

		public function __construct(KeyValueStorage $handler)
		{
			$this->handler = $handler;
		}

		public function dump()
		{
			return array_merge($this->data, $this->handler->dump());
		}

		public function get($key)
		{
			printf("mem get(): %s\n", $key);
			return isset($this->data[$key]) ? $this->data[$key] : $this->handler->get($key);
		}

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
		protected $filter = array();

		public function __construct(KeyValueStorage $handler)
		{
			$this->handler = $handler;
		}

		public function dump()
		{
			return $this->handler->dump();
		}

		public function dumpJson()
		{
			return json_encode($this->dump());
		}

		public function get($key)
		{
			if (!isset($this->filter[sha1($key)]))
			{
				printf("bloom: %s -> no\n", $key);
				return null;
			}

			printf("bloom: %s -> probably\n", $key);
			return $this->handler->get($key);
		}

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
	$storage->get("mattl");

	// Dump contents
	print_r($storage->dumpJson());
