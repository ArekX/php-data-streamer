<?php

namespace tests;

use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\PayloadMessageConverter;
use ArekX\DataStreamer\Data\Settings;
use ArekX\DataStreamer\StreamWriter;
use Codeception\Stub\Expected;
use mock\MockDriver;

class StreamWriterTest extends \Codeception\Test\Unit
{
    public function testWritingArrayMessage()
    {
        $driver = new MockDriver();

        $writer = new StreamWriter($driver, new Settings([
            'stream' => 'test'
        ]), new PayloadMessageConverter());

        $payload = ['key' => 'value'];
        $writer->write(ArrayMessage::create('test-type', $payload));

        expect($driver->messages['test'])->arrayToHaveCount(1);
        expect($driver->messages['test'][0]['message']['type'])->toBe('test-type');
        expect($driver->messages['test'][0]['message']['payload'])->toBe(json_encode($payload));
    }

        public function testWritingAMessageConvertsTheValue()
    {
        $driver = new MockDriver();

        $converter = $this->make(PayloadMessageConverter::class, [
            'convert' => Expected::once(['type' => 'type', 'payload' => 'payload' ])
        ]);

        $writer = new StreamWriter($driver, new Settings([
            'stream' => 'test'
        ]), $converter);

        $writer->write(ArrayMessage::create('test-type', []));

        expect($driver->messages['test'])->arrayToHaveCount(1);
        expect($driver->messages['test'][0]['message']['type'])->toBe('type');
        expect($driver->messages['test'][0]['message']['payload'])->toBe('payload');
    }
}