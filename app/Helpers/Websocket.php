<?php
namespace App\Helpers;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface {
    protected $clients;

    protected array $rooms = [];
    protected array $users = [];
    protected array $usersInGroup = [];
    protected int $messageId = 0;

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
            //resourceId - id user
            $this->rooms[$msg->chat->id][$from->resourceId] = $from;
            $this->users[$from->resourceId] = $msg->chat->id;
            $this->usersInGroup[$msg->chat->id][$from->resourceId] = $msg->from->first_name;
            $users = [];

            foreach ($this->usersInGroup[$msg->chat->id] as $user) {
                $users[] = $user;
            }

            // $message = ['message' => 'connection', 'users' => $users];
            $this->messageId++;
            $message = [
                "update_id" => time() * 1000, // Уникальный идентификатор обновления
                'message' => $msg->message,
                "settings" => [
                    "message_id" => $this->messageId++, // Уникальный идентификатор сообщения
                    "from" => [
                        "id" => $from->resourceId, // Идентификатор пользователя *
                        "is_bot" => false,
                        "first_name" => $this->users[$from->resourceId], // Имя пользователя
                    ],
                    // "to" => [
                    //     "id" => $from->resourceId, // Идентификатор пользователя *
                    //     "is_bot" => false,
                    //     "first_name" => $this->users[$from->resourceId], // Имя пользователя
                    // ],
                    "chat" => [
                        "id" => $msg->chat->id, // Идентификатор группы *
                        "title" => empty ($msg->chat->name) ? "Название группы" : $msg->chat->name, // Название группы Не обязательно
                        // "type" => "channel", // Тип чата
                        "type" => "group", // Тип чата
                    ],
                    "date" => time(), // Текущая дата и время 
                    "text" => $msg->text, // Текст сообщения
                ],
            ];

            foreach ($this->rooms[$msg->chat->id] as $client) {
                $client->send(json_encode($message));
            }

            //   dump($this->usersInGroup[$msg->value]);
        } else if ($msg->message === 'new message') {
            $room = $this->users[$from->resourceId];
            foreach ($this->rooms[$room] as $client) {
                $message = [
                    'message' => 'message', 
                    'text' => $msg->text, 
                    'from' => [
                        "id" => $this->users[$from->resourceId] ,
                        "first_name" => $this->usersInGroup[$room][$from->resourceId],
                    ]
                ];
                print_r($message);
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
        unset($this->usersInGroup[$room][$conn->resourceId]);

        $users = [];

        foreach ($this->usersInGroup[$room] as $user) {
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