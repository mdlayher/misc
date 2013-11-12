<?php

// Adapted from: http://blog.ircmaxell.com/2013/11/beyond-object-oriented-programming.html

// Goal is to never modify existing state, but only to transform it and create new state
class HttpResponse
{
	private $status;
	private $description;
	private $headers = array();
	private $body;

	public function __construct($status, $description, array $headers, $body)
	{
		$this->status = (int)$status;
		$this->description = $description;
		$this->headers = $headers;
		$this->body = $body;
	}

	// Instance variables are immutable, and cannot be modified

	public function getStatus()
	{
		return $this->status;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function getBody()
	{
		return $this->body;
	}

	// Example: add a X-Powered-By header using this paradigm
	public function addPoweredBy()
	{
		// Never modify state, only transform and create new state
		return new HttpResponse(
			$this->getStatus(),
			$this->getDescription(),
			array_merge(array("X-Powered-By" => "PHP " . phpversion()), $this->getHeaders()),
			$this->getBody()
		);
	}

	// Example: render a 404 error
	public function render404()
	{
		return new HttpResponse(404, "Not Found", array(), "Error: 404 Not Found");
	}

	// Output true HTTP response text
	public function output()
	{
		// Status
		$buffer = "";
		$buffer .= sprintf("HTTP/1.1 %d %s\r\n", $this->getStatus(), $this->getDescription());

		// Headers
		foreach ($this->getHeaders() as $key => $value)
		{
			$buffer .= sprintf("%s: %s\r\n", $key, $value);
		}

		// Body
		$buffer .= "\r\n" . $this->getBody() . "\r\n";

		return $buffer;
	}
}

$res = new HttpResponse(200, "OK", array(), "Hello World!");
echo $res->output();
echo "\r\n";

echo $res->addPoweredBy()->output();
echo "\r\n";

echo $res->render404()->output();
echo "\r\n";
