# Redis Data Streamer

[![Documentation Status](https://readthedocs.org/projects/php-data-streamer/badge/?version=latest)](https://php-data-streamer.readthedocs.io/en/latest/?badge=latest)
[![Build Status](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/badges/build.png?b=main)](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/build-status/main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/?branch=main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/ArekX/php-data-streamer/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)

Data streaming library which handles multiple redis drivers
and provides easy access to Redis Streams and Consumer Groups.

Both php-redis and predis drivers are supported.

## Installation

Through composer: `composer require arekxv/php-data-streamer`

# Usage

This library supports both reading the data stream and writing to a
data stream.

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

# Documentation

Documentation is available at: http://php-data-streamer.rtfd.io/

You can also build documentation manually or read it directly from `docs` folder.

## Building

1. Install Python 3 and PIP
2. Run `pip install mkdocs`
3. Run `mkdocs serve` to serve or `mkdocs build` to build.

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