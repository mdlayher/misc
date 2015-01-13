#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#define BUF_SIZE 8192

// Simple implementation of `tee`,
// Exercise 4-1 of The Linux Programming Interface
int main(int argc, char **argv) {
	// Buffer for file I/O
	char buffer[BUF_SIZE];

	// Check usage
	if (argc < 2) {
		printf("usage: tee [-a] [file]\n");
		exit(1);
	}

	// Check if "-a" append flag passed, ensure one and only one
	// filename was passed
	// (getopt() is probably overkill for one option)
	int flagA = strncmp(argv[1], "-a", 2);
	if ((argc == 2 && flagA == 0) || argc > 3) {
		printf("usage: tee [-a] [file]\n");
		exit(1);
	}

	// If append flag passed, use append mode
	int append = 0;
	char *filename = argv[1];
	if (argc > 2 && flagA == 0) {
		append = 1;
		filename = argv[2];
	}

	// Set open() flags according to operation mode
	int flags;
	if (append == 1) {
		flags = O_CREAT | O_WRONLY | O_APPEND;
	} else {
		flags = O_CREAT | O_WRONLY | O_TRUNC;
	}

	// 666 permissions
	mode_t perms = S_IRUSR | S_IWUSR | S_IRGRP | S_IWGRP | S_IROTH | S_IWOTH;

	// Open output file
	int fd;
	if ((fd = open(filename, flags, perms)) == -1) {
		perror("tee: open");
		exit(1);
	}

	// Track bytes read and written
	int rn;
	int wn;

	// Loop until EOF
	for (;;) {
		// Read buffer from stdin
		if ((rn = read(STDIN_FILENO, buffer, BUF_SIZE)) == -1) {
			perror("tee: read");
			exit(1);
		}

		// If EOF, end loop
		if (rn == 0) {
			break;
		}

		// Null terminate buffer, write to stdout and file
		buffer[rn] = '\0';
		printf("%s", buffer);
		if ((wn = write(fd, buffer, rn)) != rn) {
			// System call error
			if (wn == -1) {
				perror("tee: write");
				exit(1);
			}

			// Could not write full amount
			printf("tee: short write\n");
			exit(1);
		}
	}

	// Close output file
	if (close(fd) == -1) {
		perror("tee: close");
		exit(1);
	}
}
