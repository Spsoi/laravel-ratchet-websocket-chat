<?php
namespace App\Helpers;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface {
    protected $clients;

    protected array $rooms = [];
    protected array $users = [];
    protected array $usersName = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = (json_decode($msg));
        if ($msg->message === 'new room') {
            $this->rooms[$msg->value][$from->resourceId] = $from;
            $this->users[$from->resourceId] = $msg->value;
            $this->usersName[$msg->value][$from->resourceId] = $msg->user;
            $users = [];

            foreach ($this->usersName[$msg->value] as $user) {
                $users[] = $user;
            }

            $message = ['message' => 'connection', 'users' => $users];

            foreach ($this->rooms[$msg->value] as $client) {
                $client->send(json_encode($message));
            }

              dump($this->usersName[$msg->value]);
        } else if ($msg->message === 'new message') {
            $room = $this->users[$from->resourceId];
            foreach ($this->rooms[$room] as $client) {
                $message = ['message' => 'message', 'value' => $msg->value, 'user' => $this->usersName[$room][$from->resourceId]];
                $client->send(json_encode($message));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $room = $this->users[$conn->resourceId];
        unset($this->rooms[$room][$conn->resourceId]);
        unset($this->users[$conn->resourceId]);
        unset($this->usersName[$room][$conn->resourceId]);

        $users = [];

        foreach ($this->usersName[$room] as $user) {
            $users[] = $user;
        }

        $message = ['message' => 'connection', 'users' => $users];

        foreach ($this->rooms[$room] as $client) {
            $client->send(json_encode($message));
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}