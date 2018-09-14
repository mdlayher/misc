// Package bst implements a basic binary search tree for string key/value pairs.
package bst

import (
	"bufio"
	"fmt"
	"io"
	"strings"
)

// Order specifies a tree traversal order for a Tree's Walk method.
type Order int

// Possible Order values.
const (
	PreOrder Order = iota
	InOrder
	PostOrder
)

// A Tree is a basic binary search tree.
type Tree struct {
	root *Node
}

// ParseTree creates a Tree using an input series of "key,value" pair strings
// separated by newlines.
func ParseTree(r io.Reader) (*Tree, error) {
	s := bufio.NewScanner(r)

	var t Tree
	for s.Scan() {
		text := s.Text()
		ss := strings.Split(text, ",")
		if len(ss) != 2 {
			return nil, fmt.Errorf("bst: malformed tree entry: %q", text)
		}

		t.Insert(ss[0], ss[1])
	}

	if err := s.Err(); err != nil {
		return nil, err
	}

	return &t, nil
}

// Insert inserts a new key/value pair Node into the Tree.
func (t *Tree) Insert(key, value string) {
	if t.root == nil {
		t.root = node(key, value)
		return
	}

	t.root.insert(node(key, value))
}

// Walk walks each Node in the tree using the specified Order.
func (t *Tree) Walk(order Order, fn func(n *Node)) {
	if t.root == nil {
		return
	}

	t.root.walk(order, fn)
}

// Search determines if key is present in the tree, returning its value and
// whether or not the key was found.
func (t *Tree) Search(key string) (string, bool) {
	if t.root == nil {
		return "", false
	}

	return t.root.search(key)
}

// node is a shortcut for creating a Node.
func node(key, value string) *Node {
	return &Node{
		Key:   key,
		Value: value,
	}
}

// A Node is a key/value pair Node in a Tree.
type Node struct {
	Key   string
	Value string

	left  *Node
	right *Node
}

func (n *Node) insert(x *Node) {
	// Pick which pointer we are using to insert the new Node.
	ptr := &n.left
	if n.Key < x.Key {
		ptr = &n.right
	}

	if *ptr != nil {
		// Recursively insert the node.
		(*ptr).insert(x)
	} else {
		// Pointer is nil, add a new node.
		*ptr = x
	}
}

func (n *Node) walk(order Order, fn func(n *Node)) {
	if n == nil {
		return
	}

	// The order these functions are invoked is determined by the Order value.
	var (
		rootFn  = func() { fn(n) }
		leftFn  = func() { n.left.walk(order, fn) }
		rightFn = func() { n.right.walk(order, fn) }
	)

	var try [3]func()
	switch order {
	case PreOrder:
		try = [3]func(){rootFn, leftFn, rightFn}
	case InOrder:
		try = [3]func(){leftFn, rootFn, rightFn}
	case PostOrder:
		try = [3]func(){leftFn, rightFn, rootFn}
	default:
		panic("bst: Node.walk called with unknown Order value")
	}

	for _, fn := range try {
		fn()
	}
}

func (n *Node) search(key string) (string, bool) {
	if n == nil {
		return "", false
	}

	switch strings.Compare(key, n.Key) {
	case 0:
		return n.Value, true
	case -1:
		return n.left.search(key)
	case 1:
		return n.right.search(key)
	}

	panic("bst: Node.search hit unreachable code")
}
