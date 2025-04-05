<?php


namespace App\Extensions\Centrifuge;


use App\Extensions\Centrifuge\Messages\MessageInterface;
use phpcent\Client;

class Centrifuge
{

    protected Client $client;

    public function __construct()
    {
        $host = env('CENTRIFUGE_HOST');
        $port = env('CENTRIFUGE_PORT');
        $apiPrefix = env('CENTRIFUGE_API_PREFIX');
        $apiKey = env('CENTRIFUGE_API_KEY');
        $secretKey = env('CENTRIFUGE_SECRET_KEY');

        $this->client = new Client("$host:$port$apiPrefix/api");
        $this->client->setApiKey($apiKey);
        $this->client->setSecret($secretKey);
    }

    public function publishMessage(string $channel, MessageInterface $msg)
    {
        $this->client->publish($channel, ["data" => $msg->getMessage()]);
    }

    public function makeToken(string|int $userId, int $expired = 0, array $channels = array(), array $meta = array()): string
    {
        return $this->client->generateConnectionToken($userId, $expired, $channels, $meta);
    }
}
