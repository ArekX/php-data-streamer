# Redis Data Streamer

Data streaming library which handles multiple redis drivers
and provides easy access to Redis Streams and Consumer Groups.

# Usage

## Reader mode

To set your code to listen and read messages the code should be setup as follows:

```php
use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\CallableHandler;
use ArekX\DataStreamer\Data\CallableParser;
use ArekX\DataStreamer\Data\Settings;
use ArekX\DataStreamer\Drivers\RedisDriver;
use ArekX\DataStreamer\StreamReader;

$driver = new RedisDriver(); // or new \ArekX\DataStreamer\Drivers\PredisDriver() if Predis package is used
$driver->connect([
    'host' => '127.0.0.1'
]);

$parser = new CallableParser();
$parser->setDefaultBuilder(fn(string $id, string $type, array $payload) => ArrayMessage::create($type, $payload, $id));

$handler = new CallableHandler();
$handler->setDefaultHandler(function (ArrayMessage $message) {
    echo "{$message->getId()}: " . json_encode($message->getPayload()) . PHP_EOL;
    return true; // Returning true means that message was handled successfully.
});

$settings = new Settings([
    'stream' => 'data-stream',
    'consumerGroup' => 'my-consumer-' . $argv[1],
    'consumerName' => 'my-consumer-consumer',
]);

$reader = new StreamReader($driver, $parser, $handler, $settings);

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

$driver = new RedisDriver(); // or new \ArekX\DataStreamer\Drivers\PredisDriver() if Predis package is used
$driver->connect([
    'host' => '127.0.0.1'
]);

$settings = new Settings([
    'stream' => 'data-stream'
]);

$converter = new PayloadMessageConverter();
$writer = new StreamWriter($driver, $settings, $converter);


for($i = 0; $i < 50; $i++) {
    $writer->write(ArrayMessage::create('test-type', [
        'key' => 'value',
        'key2' => 'value2'
    ]));
}
```

# Tests

To run tests run `composer test`.

# License

Copyright Aleksandar Panic

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0 or [in this repository](LICENSE.md)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.