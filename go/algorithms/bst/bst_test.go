package bst_test

import (
	"strings"
	"testing"

	"github.com/google/go-cmp/cmp"

	"github.com/mdlayher/misc/go/algorithms/bst"
)

const in = `foo,bar
abc,def
bar,baz
qux,corge
012,123
`

func TestTree(t *testing.T) {
	tree, err := bst.ParseTree(strings.NewReader(in))
	if err != nil {
		t.Fatalf("failed to parse tree: %v", err)
	}

	var got []kv
	tree.Walk(bst.InOrder, func(n *bst.Node) {
		got = append(got, newKV(n.Key, n.Value))
	})

	want := []kv{
		newKV("012", "123"),
		newKV("abc", "def"),
		newKV("bar", "baz"),
		newKV("foo", "bar"),
		newKV("qux", "corge"),
	}

	if diff := cmp.Diff(want, got); diff != "" {
		t.Fatalf("unexpected key/value pairs (-want +got):\n%s", diff)
	}

	if _, ok := tree.Search("012"); !ok {
		t.Fatal("couldn't find key in tree")
	}
	if _, ok := tree.Search("xxx"); ok {
		t.Fatal("found unexpected key in tree")
	}
}

func newKV(key, value string) kv {
	return kv{Key: key, Value: value}
}

type kv struct {
	Key, Value string
}
