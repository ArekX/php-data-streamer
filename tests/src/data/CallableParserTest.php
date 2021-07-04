<?php
namespace tests\data;

use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\CallableParser;

class CallableParserTest extends \Codeception\Test\Unit
{
    public function testParserByType()
    {
        $parser = new CallableParser();
        $parser->setBuilder('type1', function($id, $type, $payload) {
            return ArrayMessage::create($type, $payload, $id);
        });

        $message = $parser->parse('id-1', [
            'type' => 'type1',
            'payload' => json_encode(['payload-1'])
        ]);

        expect($message->getId())->toBe('id-1');
        expect($message->getPayload())->toBe(['payload-1']);
        expect($message->getType())->toBe('type1');
    }

    public function testParserNonExistingType()
    {
        $parser = new CallableParser();
        $parser->setBuilder('type1', function($id, $type, $payload) {
            return ArrayMessage::create($type, $payload, $id);
        });

        $this->expectException(\Exception::class);
        $parser->parse('id-1', [
            'type' => 'type2',
            'payload' => json_encode(['payload-1'])
        ]);
    }

    public function testDefaultParser()
    {
        $parser = new CallableParser();
        $parser->setDefaultBuilder(function($id, $type, $payload) {
            return ArrayMessage::create($type, $payload, $id);
        });

        $message = $parser->parse('id-521', [
            'type' => 'type-unknown',
            'payload' => json_encode(['payload-4'])
        ]);

        expect($message->getId())->toBe('id-521');
        expect($message->getPayload())->toBe(['payload-4']);
        expect($message->getType())->toBe('type-unknown');
    }
}