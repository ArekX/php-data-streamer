<?php
namespace tests\data;

use ArekX\DataStreamer\Data\Settings;

class SettingsTest extends \Codeception\Test\Unit
{
    public function testDefaultSettings()
    {
        $settings = new Settings([]);
        expect($settings->getConsumerGroup())->toBe('');
        expect($settings->getConsumerName())->toBe('');
        expect($settings->getStreamName())->toBe('');
        expect($settings->shouldReadFromStart())->toBeTrue();
        expect($settings->getMessageReadCount())->toBe(1);
        expect($settings->getMessageWaitTimeout())->toBe(10000);
    }

    public function testOverrideSettings()
    {
        $arraySettings = [
            'stream' => 'stream-name',
            'consumerGroup' => 'stream-consumer-group',
            'consumerName' => 'stream-consumer-name',
            'messagesPerRead' => 5,
            'waitTimeout' => 5
        ];

        $settings = new Settings($arraySettings);
        expect($settings->getConsumerGroup())->toBe($arraySettings['consumerGroup']);
        expect($settings->getConsumerName())->toBe($arraySettings['consumerName']);
        expect($settings->getStreamName())->toBe($arraySettings['stream']);
        expect($settings->getMessageReadCount())->toBe($arraySettings['messagesPerRead']);
        expect($settings->getMessageWaitTimeout())->toBe($arraySettings['waitTimeout']);
    }
}