package hashtable_test

import (
	"strings"
	"testing"

	"github.com/google/go-cmp/cmp"
	"github.com/mdlayher/misc/go/algorithms/hashtable"
)

const in = `4
foo,bar
abc,def
bar,baz
qux,corge
012,123
`

func TestTable(t *testing.T) {
	table, err := hashtable.Parse(strings.NewReader(in))
	if err != nil {
		t.Fatalf("failed to parse hashtable: %v", err)
	}

	if _, ok := table.Get("xxx"); ok {
		t.Fatal("found unexpected xxx value")
	}

	const value = "ok"
	table.Insert("foo", value)

	v, ok := table.Get("foo")
	if !ok {
		t.Fatal("could not find foo value")
	}

	if diff := cmp.Diff(value, v); diff != "" {
		t.Fatalf("unexpected foo value (-want +got):\n%s", diff)
	}
}
