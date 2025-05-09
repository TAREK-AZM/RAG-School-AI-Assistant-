<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Wire\AMQPTable;
use Illuminate\Support\Facades\Log;

class SendMessage extends Command
{
    protected $signature = 'rabbitmq:send';
    protected $description = 'Send Hello World message to RabbitMQ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Attempting to connect to RabbitMQ...');
        
        try {
            // Get connection parameters from environment variables with fallbacks
            $host = env('RABBITMQ_HOST', 'localhost');
            $port = (int)env('RABBITMQ_PORT', 15672);
            $user = env('RABBITMQ_USER', 'tarek');
            $password = env('RABBITMQ_PASSWORD', 'ensat');
            $vhost = env('RABBITMQ_VHOST', '/');
            
            $this->info("Connecting to RabbitMQ at {$host}:{$port}");
           

            $connection  = new AMQPStreamConnection(
                '127.0.0.1',
                5672,
                'guest',
                'guest',
                '/'
            );
            
            $this->info('Successfully connected to RabbitMQ');
            $channel = $connection->channel();
            
            // Declare the queue
            $this->info('Declaring queue: hello');
            $channel->queue_declare('hello', false, false, false, false);
            
            // Create a message
            $messageBody = 'Hello World!';
            $message = new AMQPMessage($messageBody);
            
            // Publish the message to the queue
            $this->info('Publishing message: ' . $messageBody);
            $channel->basic_publish($message, '', 'hello');
            
            $this->info(" [x] Sent '{$messageBody}'");
            
            // Close the channel and connection
            $channel->close();
            $connection->close();
            
        } catch (AMQPIOException $e) {
            $this->error('Connection Error: ' . $e->getMessage());
            $this->info("\nTroubleshooting tips:");
            $this->info("1. Verify RabbitMQ is running: docker ps");
            $this->info("2. Make sure you're using the correct port (5672 for AMQP, not 15672 for management)");
            $this->info("3. Try these different host configurations:");
            $this->info("   - localhost");
            $this->info("   - 127.0.0.1");
            $this->info("   - host.docker.internal");
            $this->info("   - [Docker container IP, run: docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' rabbitmq]");
            $this->info("4. Check if you can access RabbitMQ management UI: http://localhost:15672 (user: guest, pass: guest)");
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }


    public static function sendToRabbitMQ(array $data)
{
    try {
        // Connect to RabbitMQ
        $connection = new AMQPStreamConnection(
            '127.0.0.1',
            5672,
            'guest',
            'guest',
            '/'
        );
        
        $channel = $connection->channel();
        
        // Declare queue as durable to match Python consumer
        $channel->queue_declare(
            'file_storage', 
            false,
            true,  // Set durable to true
            false,
            false
        );
        
        // Get file content as binary data
        $fileContent = null;
        if (isset($data['file_content']) && is_string($data['file_content'])) {
            // If content is already provided as string
            $fileContent = $data['file_content'];
        } elseif (isset($data['file']) && is_object($data['file']) && method_exists($data['file'], 'getRealPath')) {
            // If an UploadedFile object is provided
            $fileContent = file_get_contents($data['file']->getRealPath());
        } else {
            throw new \Exception("No valid file or content provided");
        }
        
        // Prepare metadata
        $metadata = json_encode([
            'file_name' => $data['file_name'],
            'provider' => $data['provider'] ?? 'unknown',
            'mime_type' => $data['mime_type'] ?? 'application/octet-stream',
            'size' => strlen($fileContent)
        ]);
        
        // Set custom headers to hold metadata
        $headers = new AMQPTable([
            'x-file-name' => $data['file_name'],
            'x-provider' => $data['provider'] ?? 'unknown',
            'x-mime-type' => $data['mime_type'] ?? 'application/octet-stream',
            'x-metadata' => $metadata
        ]);
        
        // Log what we're sending (not the content)
        Log::info("Sending file to RabbitMQ", [
            'file_name' => $data['file_name'],
            'size' => strlen($fileContent),
            'mime_type' => $data['mime_type'] ?? 'application/octet-stream'
        ]);
        
        // Send the binary content directly
        $channel->basic_publish(
            new AMQPMessage(
                $fileContent,  // Raw binary content
                [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                    'content_type' => $data['mime_type'] ?? 'application/octet-stream',
                    'application_headers' => $headers
                ]
            ),
            '',
            'file_storage'
        );
        
        $channel->close();
        $connection->close();
        
        return true;
    } catch (\Exception $e) {
        Log::error("RabbitMQ Error: " . $e->getMessage());
        throw $e;
    }
}
}