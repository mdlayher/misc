package main

import (
	"fmt"
	"io"
	"log"
	"net"
	"net/http"
	"os/user"
	"strconv"
	"unsafe"

	"golang.org/x/sys/unix"
)

func main() {
	l, err := net.Listen("unix", "/tmp/unixhttp.sock")
	if err != nil {
		log.Fatalf("failed to listen: %v", err)
	}

	http.Serve(l, http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		hj, ok := w.(http.Hijacker)
		if !ok {
			http.Error(w, "webserver doesn't support hijacking", http.StatusInternalServerError)
			return
		}

		c, rw, err := hj.Hijack()
		if err != nil {
			http.Error(w, err.Error(), http.StatusInternalServerError)
			return
		}
		defer c.Close()

		uc, ok := c.(*net.UnixConn)
		if !ok {
			panicf("not a net.UnixConn: %T", c)
		}

		sc, err := uc.SyscallConn()
		if err != nil {
			panicf("failed to create syscall conn: %v", err)
		}

		var cred unix.Ucred
		err = sc.Control(func(fd uintptr) {
			err := getsockopt(
				int(fd),
				unix.SOL_SOCKET,
				unix.SO_PEERCRED,
				unsafe.Pointer(&cred),
				int(unsafe.Sizeof(cred)),
			)
			if err != nil {
				panicf("failed to getsockopt: %v", err)
			}
		})
		if err != nil {
			panicf("failed to call control: %v", err)
		}

		userInfo, err := user.LookupId(strconv.Itoa(int(cred.Uid)))
		if err != nil {
			panicf("failed to get user: %v", err)
		}

		group, err := user.LookupGroupId(strconv.Itoa(int(cred.Gid)))
		if err != nil {
			panicf("failed to get user: %v", err)
		}

		log.Println(userInfo, group)

		_, _ = io.WriteString(rw, "HTTP/1.1 200 OK\n\n")
		_, _ = io.WriteString(rw, fmt.Sprintf("%s:%s", userInfo.Username, group.Name))

		if err := rw.Flush(); err != nil {
			panicf("failed to flush: %v", err)
		}
	}))
}

func panicf(format string, a ...interface{}) {
	panic(fmt.Sprintf(format, a...))
}

func getsockopt(s int, level, name int, val unsafe.Pointer, vallen int) error {
	_, _, errno := unix.Syscall6(unix.SYS_GETSOCKOPT, uintptr(s), uintptr(level), uintptr(name), uintptr(val), uintptr(unsafe.Pointer(&vallen)), 0)
	if errno != 0 {
		return unix.Errno(errno)
	}

	return nil
}
