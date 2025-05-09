<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Exception;
class ReceiveMessage extends Command
{
    protected $signature = 'rabbitmq:receive';
    protected $description = 'Receive Hello World message from RabbitMQ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Establish connection to RabbitMQ
        // $connection = new AMQPStreamConnection(
        //     '127.0.0.1',    // Use 127.0.0.1 (not 'localhost')
        //     5672,            // AMQP port (from docker port output)
        //     'guest',         // username
        //     'guest',         // password
        //     '/',             // vhost
        //     false,           // insist
        //     'AMQPLAIN',      // login method
        //     null,            // login response
        //     'en_US',         // locale
        //     30.0,            // connection timeout (increased)
        //     30.0             // read/write timeout (increased)
        // );        
        
        $connection  = new AMQPStreamConnection(
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
        
        $channel = $connection->channel();

        // Declare the queue
        $channel->queue_declare('hello', false, false, false, false);

        // Define the callback function to handle the message
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            $msg->ack();
        };

        // Set up the consumer to listen to the queue
        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        // Start consuming messages
       try{

            // Continuous message consumption
            while (count($channel->callbacks)) {
                $channel->wait();
            }

       }catch(Exception $e){
           echo $e->getMessage();
       }
        // Close the channel and connection when done
        $channel->close();
        $connection->close();
    }

    public static function sendToRabbitMQ(array $data)
{
    try {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'tarek'),
            env('RABBITMQ_PASSWORD', 'ensat')
        );
        
        $channel = $connection->channel();
        
        $channel->queue_declare(
            'file_storage', 
            false, 
            true,  // Durable queue
            false, 
            false
        );
        
        $message = new AMQPMessage(
            json_encode([
                'file_content' => base64_encode($data['file_content']),
                'file_name' => $data['file_name'],
                'metadata' => [
                    'uploaded_at' => now()->toISOString(),
                    'source' => 'laravel'
                ]
            ]),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        
        $channel->basic_publish($message, '', 'file_storage');
        
        $channel->close();
        $connection->close();
        
    } catch (\Exception $e) {
        // \Log::error("RabbitMQ Error: " . $e->getMessage());
        throw $e;
    }
}

}
