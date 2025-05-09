<?php
require __DIR__.'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

try {
    echo "Connecting to RabbitMQ...\n";
    
    $connection = new AMQPStreamConnection(
        '127.0.0.1',
        5672,
        'tarek',
        'ensat',
        '/',
        false,
        'AMQPLAIN',
        null,
        'en_US',
        30.0,
        30.0
    );
    
    echo "Successfully connected!\n";
    
    $channel = $connection->channel();
    $channel->queue_declare('test_queue', false, true, false, false);
    echo "Queue declared\n";
    
    $channel->close();
    $connection->close();
    
} catch (\Exception $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}