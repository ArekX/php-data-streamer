<?php
namespace tests;

use ArekX\DataStreamer\Contracts\Driver;
use ArekX\DataStreamer\Contracts\FailHandler;
use ArekX\DataStreamer\Contracts\Message;
use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\CallableHandler;
use ArekX\DataStreamer\Data\CallableParser;
use ArekX\DataStreamer\Data\PayloadMessageConverter;
use ArekX\DataStreamer\Data\Settings;
use ArekX\DataStreamer\StreamReader;
use ArekX\DataStreamer\StreamWriter;
use mock\MockDriver;
use mock\MockFailHandler;

class StreamReaderTest extends \Codeception\Test\Unit
{
    public function testProcessingAMessage()
    {
        $driver = $this->createDriver();

        $settings = new Settings([]);

        $handledMessage = null;

        $reader = $this->createReader($driver, $settings, [
            'test' => function(Message $message) use(&$handledMessage) {
                $handledMessage = $message;
                return true;
            }
        ]);

        $writer = new StreamWriter($driver, $settings, new PayloadMessageConverter());

        $writer->write(ArrayMessage::create('test', ['payload' => true]));

        $reader->processPendingMessages();

        expect($handledMessage->getType())->toBe('test');
        expect($handledMessage->getPayload())->toBe(['payload' => true]);
    }

    public function testFirstRead()
    {
        $handledMessage = null;

        $reader = $this->createReader($this->createDriver(), new Settings([]), [
            'test' => function(Message $message) use(&$handledMessage) {
                $handledMessage = $message;
                return true;
            }
        ]);

        expect($reader->getReadFrom())->toBe(Driver::FROM_START);

        $reader->processPendingMessages();

        expect($reader->getReadFrom())->toBe(Driver::FROM_LATEST);

        expect($handledMessage)->toBeNull();
    }

     public function testFailureHandler()
    {
        $driver = $this->createDriver();

        $settings = new Settings([]);

        $failHandler = new MockFailHandler();

        $handledMessage = null;

        $error = new \Exception('Test');

        $reader = $this->createReader($driver, $settings, [
            'test' => function(Message $message) use(&$handledMessage, $error) {
                $handledMessage = $message;
                throw $error;
            }
        ], $failHandler);

        $writer = new StreamWriter($driver, $settings, new PayloadMessageConverter());

        $writer->write(ArrayMessage::create('test', ['payload']));

        $reader->processPendingMessages();

        expect($failHandler->failedItems[0][0]['id'])->toBe($handledMessage->getId());
        expect($failHandler->failedItems[0][0]['raw'])->toBe([
            'type' => $handledMessage->getType(),
            'payload' => json_encode($handledMessage->getPayload())
        ]);
        expect($failHandler->failedItems[0][0]['error'])->toBe($error);
    }

    public function testThrowErrorIfThereIsNoFailureHandler()
    {
        $driver = $this->createDriver();

        $settings = new Settings([]);

        $reader = $this->createReader($driver, $settings, [
            'test' => function() {
                throw new \Exception('Test');
            }
        ]);

        $writer = new StreamWriter($driver, $settings, new PayloadMessageConverter());

        $writer->write(ArrayMessage::create('test', ['payload']));

        $this->expectException(\Exception::class);
        $reader->processPendingMessages();
    }

    protected function createDriver()
    {
         return new MockDriver();
    }

    protected function createReader(Driver $driver, Settings $settings, array $handlers, FailHandler $failHandler = null)
    {
        $parser = new CallableParser();
        $parser->setDefaultBuilder(function($id, $type, $payload) {
            return ArrayMessage::create($type, $payload, $id);
        });

        $handler = new CallableHandler();
        foreach ($handlers as $type => $handlerCallback) {
            $handler->setHandler($type, $handlerCallback);
        }

        $stream = new StreamReader($driver, $parser, $handler, $settings, $failHandler);
        $stream->initializeStream();

        return $stream;
    }
}