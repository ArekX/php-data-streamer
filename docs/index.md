# PHP Data Streamer


## Reading and handling messages

To set your code to listen and read messages the code should be setup as follows:

```php
use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\CallableHandler;
use ArekX\DataStreamer\Data\CallableParser;
use ArekX\DataStreamer\Data\Settings;
use ArekX\DataStreamer\Drivers\RedisDriver;
use ArekX\DataStreamer\StreamReader;

// Specify a redis driver to use
$driver = new RedisDriver(); // or new \ArekX\DataStreamer\Drivers\PredisDriver() if Predis package is used
$driver->connect([
    'host' => '127.0.0.1'
]);

// Specify a message converter from array into instances
// This can be one callable via setDefaultBuilder or per type in setBuilder
$parser = new CallableParser();
$parser->setDefaultBuilder(fn(string $id, string $type, array $payload) => ArrayMessage::create($type, $payload, $id));

// Set handler for messages this can be a default one for all messages
// or a custom one per Message::getType()
$handler = new CallableHandler();
$handler->setDefaultHandler(function (ArrayMessage $message) {
    echo "{$message->getId()}: " . json_encode($message->getPayload()) . PHP_EOL;
    return true; // Returning true means that message was handled successfully.
});

// Settings object which holds the configuration for the stream.
$settings = new Settings([
    'stream' => 'data-stream',
    'consumerGroup' => 'my-consumer-group',
    'consumerName' => 'my-consumer-consumer',
]);

// Initialize data stream reader.
$reader = new StreamReader($driver, $parser, $handler, $settings);

// Run infinite loop to process messages.
echo "Listening..." . PHP_EOL;
$reader->runLoop();
```

## Sending messages

To send messages you can send the data using code below:

```php
use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\PayloadMessageConverter;
use ArekX\DataStreamer\Data\Settings;
use ArekX\DataStreamer\Drivers\RedisDriver;
use ArekX\DataStreamer\StreamWriter;

// Specify a redis driver to use
$driver = new RedisDriver(); // or new \ArekX\DataStreamer\Drivers\PredisDriver() if Predis package is used
$driver->connect([
    'host' => '127.0.0.1'
]);

// Settings object which holds the configuration for the stream.
$settings = new Settings([
    'stream' => 'data-stream'
]);

// Define a converter which will convert a message into an array
// suitable for sending across the data stream.
$converter = new PayloadMessageConverter();

// Initialize a stream writer
$writer = new StreamWriter($driver, $settings, $converter);

// Send message to the data stream
$writer->write(ArrayMessage::create('test-type', [
    'key' => 'value',
    'key2' => 'value2'
]));
```
