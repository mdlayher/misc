// Simple TCP sockets echo server example
// Matt Layher, 2/20/13

#include <stdio.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

// Maximum number of connection requests
#define QUEUE_LENGTH 10

// Define listening port
#define PORT 4000

// Define send/receive buffer length
#define BUF_LENGTH 512

// Echo function for client handling
void echo(int client_fd);

// Program entry point
int main(int argc, char *argv[])
{
	// Socket descriptors server and client
	int server_fd, client_fd;

	// Server and client address structs
	struct sockaddr_in server_address, client_address;

	// Length of client address struct
	unsigned int client_length;

	// Server port
	unsigned short port = PORT;

	// Create socket to handle incoming connections
	if ((server_fd = socket(PF_INET, SOCK_STREAM, IPPROTO_TCP)) < 0)
	{
		printf("socket() failed");
		exit(-1);
	}

	// Nullify and set up server address struct
	memset(&server_address, 0, sizeof(server_address));
	server_address.sin_family = AF_INET;				// IPv4
	server_address.sin_addr.s_addr = htonl(INADDR_ANY); // Any network interface
	server_address.sin_port = htons(port);				// Local port

	// Bind socket to local address
	if (bind(server_fd, (struct sockaddr *)&server_address, sizeof(server_address)) < 0)
	{
		printf("bind() failed");
		exit(-1);
	}

	// Begin listening on socket
	if (listen(server_fd, QUEUE_LENGTH) < 0)
	{
		printf("listen() failed");
		exit(-1);
	}

	printf("echo: listening on port %d\n", port);

	// Loop infinitely
	while (1)
	{
		// Capture size of client address struct
		client_length = sizeof(client_address);

		// Block to begin accepting client connections
		if ((client_fd = accept(server_fd, (struct sockaddr *)&client_address, &client_length)) < 0)
		{
			printf("accept() failed");
			exit(-1);
		}

		// After accept, stop blocking and send client to echo service
		printf("client connected: %s\n", inet_ntoa(client_address.sin_addr));
		echo(client_fd);
	}
}

// Echo function to handle incoming connections
void echo(int client_fd)
{
	// Input buffer and received size for client
	char in[BUF_LENGTH] = { '\0' };
	int in_size;

	// Receive client message
	if ((in_size = recv(client_fd, in, BUF_LENGTH, 0)) < 0)
	{
		printf("recv() failed");
		exit(-1);
	}

	// Continue echoing messages until client sends "/quit"
	while (strncmp(in, "/quit", 5) != 0)
	{
		// Return message to client
		if (send(client_fd, in, in_size, 0) != in_size)
		{
			printf("send() failed");
			exit(-1);
		}

		// Receive next message
		if ((in_size = recv(client_fd, in, BUF_LENGTH, 0)) < 0)
		{
			printf("recv() failed");
			exit(-1);
		}
	}

	// Close client connection
	close(client_fd);
	return;
}
