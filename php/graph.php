<?php
// Graph data structure class in PHP
// Matt Layher, 8/8/13
// Credit: http://phpmaster.com/data-structures-4/

class Graph
{
	// Input adjacency list
	protected $graph;

	// Points we've visited
	protected $visited = array();

	public function __construct($graph)
	{
		$this->graph = $graph;
	}

	// Search using a breadth first approach
	public function breadthFirstSearch($start, $end)
	{
		// Initialize all nodes as unvisited
		foreach ($this->graph as $k => $v)
		{
			$this->visited[$k] = false;
		}

		// Create queue, add starting vertex, mark it as visited
		$queue = new SplQueue();
		$queue->enqueue($start);
		$this->visited[$start] = true;

		// Generate a doubly linked list to store our pathing
		$path = array();
		$path[$start] = new SplDoublyLinkedList();

		// Use as queue, traverse elements instead of deleting them
		$path[$start]->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP);

		// Set starting point
		$path[$start]->push($start);

		// Iterate until queue is empty or target found
		while (!$queue->isEmpty() && $queue->bottom() != $end)
		{
			// Get current node
			$t = $queue->dequeue();

			// Make sure it's in the graph
			if (isset($this->graph[$t]))
			{
				// Iterate all child nodes
				foreach ($this->graph[$t] as $vertex)
				{
					// Check if we've already visited them
					if (!$this->visited[$vertex])
					{
						// If not, queue child node, mark it visited
						$queue->enqueue($vertex);
						$this->visited[$vertex] = true;

						// Add child node to our current path
						$path[$vertex] = clone $path[$t];
						$path[$vertex]->push($vertex);
					}
				}
			}
		}

		// Did we find target?
		if (isset($path[$end]))
		{
			// Convert path to a standard array
			$out = array();
			foreach ($path[$end] as $p)
			{
				$out[] = $p;
			}

			// Return path
			return $out;
		}

		// No target found
		return null;
	}
}

// Create adjacency lists, to mark which nodes point to which other nodes
$adj = array(
	"A" => array("B"),
	"B" => array("A", "D"),
	"C" => array("F"),
	"D" => array("B", "E"),
	"E" => array("B", "D", "F"),
	"F" => array("A", "E", "C"),
);

// Create a graph using the list
$graph = new Graph($adj);

// Perform a couple searches for least hops to a point
print_r($graph->breadthFirstSearch("A", "D"));
print_r($graph->breadthFirstSearch("A", "C"));
