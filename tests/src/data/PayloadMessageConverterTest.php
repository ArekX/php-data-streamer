<?php
namespace tests\data;

use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\PayloadMessageConverter;

class PayloadMessageConverterTest extends \Codeception\Test\Unit
{
    public function testConverter()
    {
        $converter = new PayloadMessageConverter();
        $message = ArrayMessage::create('typ-1', ['payload-5']);
        $result = $converter->convert($message);

        expect($result['type'])->toBe($message->getType());
        expect($result['payload'])->toBe(json_encode($message->getPayload()));
    }
}