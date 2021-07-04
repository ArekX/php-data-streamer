<?php
namespace tests\data;

use ArekX\DataStreamer\Data\ArrayMessage;

class ArrayMessageTest extends \Codeception\Test\Unit
{
    public function testCreatingAMessage()
    {
        $message = ArrayMessage::create('array-message', ['payload']);

        expect($message->getType())->toBe('array-message');
        expect($message->getPayload())->toBe(['payload']);
        expect($message->getId())->toBeNull();
    }
}