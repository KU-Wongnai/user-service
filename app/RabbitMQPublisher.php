<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    private $connection;
    private $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'rabbitmq'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'user'),
            env('RABBITMQ_PASSWORD', 'password'),
            env('RABBITMQ_VHOST', '/')
        );

        $this->channel = $this->connection->channel();
    }

    public function declareExchange($exchangeName, $exchangeType = 'direct')
    {
        $this->channel->exchange_declare($exchangeName, $exchangeType, false, true, false);
    }


    public function publish($message, $exchange = '', $routingKey = '')
    {
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}