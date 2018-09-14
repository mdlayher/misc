// Package hashtable implements a basic hashtable for string key/value pairs.
package hashtable

import (
	"bufio"
	"fmt"
	"hash/fnv"
	"io"
	"strconv"
	"strings"
)

// A Table is a basic hashtable.
type Table struct {
	m     int
	table [][]kv
}

// A kv stores key/value data in a Table.
type kv struct {
	Key, Value string
}

// NewTable creates a Table with m internal buckets.
func NewTable(m int) *Table {
	return &Table{
		m:     m,
		table: make([][]kv, m),
	}
}

// Parse creates a Table using input data.  The stream is expected to start
// with an integer that specifies M for a new Table, and then "key,value" pair
// strings separated by newlines:
//
//  8
//  key,value
//  foo,bar
func Parse(r io.Reader) (*Table, error) {
	s := bufio.NewScanner(r)

	s.Scan()
	text := s.Text()
	m, err := strconv.Atoi(text)
	if err != nil {
		return nil, fmt.Errorf("hashtable: malformed M value: %q", text)
	}

	t := NewTable(m)
	for s.Scan() {
		text := s.Text()
		ss := strings.Split(text, ",")
		if len(ss) != 2 {
			return nil, fmt.Errorf("hashtable: malformed table entry: %q", text)
		}

		t.Insert(ss[0], ss[1])
	}

	if err := s.Err(); err != nil {
		return nil, err
	}

	return t, nil
}

// Get determines if key is present in the hashtable, returning its value and
// whether or not the key was found.
func (t *Table) Get(key string) (string, bool) {
	i := t.hash(key)

	for j, kv := range t.table[i] {
		if key == kv.Key {
			return t.table[i][j].Value, true
		}
	}

	return "", false
}

// Insert inserts a new key/value pair into the Table.
func (t *Table) Insert(key, value string) {
	i := t.hash(key)

	for j, kv := range t.table[i] {
		if key == kv.Key {
			// Overwrite previous value for the same key.
			t.table[i][j].Value = value
			return
		}
	}

	// Add a new value to the table.
	t.table[i] = append(t.table[i], kv{
		Key:   key,
		Value: value,
	})
}

// hash picks a hashtable index to use to store a string with key s.
func (t *Table) hash(s string) int {
	// Good enough.
	h := fnv.New32()
	h.Write([]byte(s))
	return int(h.Sum32()) % t.m
}
