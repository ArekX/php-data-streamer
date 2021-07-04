<?php
namespace tests\data;

use ArekX\DataStreamer\Data\ArrayMessage;
use ArekX\DataStreamer\Data\CallableHandler;

class CallableHandlerTest extends \Codeception\Test\Unit
{
    public function testTypeHandlers()
    {
        $handler = new CallableHandler();
        $called1 = false;
        $called2 = false;

        $handler->setHandler('type1', function() use(&$called1) {
            $called1 = true;
        });
        $handler->setHandler('type2', function () use(&$called2) {
            $called2 = true;
        });

        expect($called1)->toBeFalse();
        expect($called2)->toBeFalse();

        $handler->handle(ArrayMessage::create('type1', []));
        $handler->handle(ArrayMessage::create('type2', []));

        expect($called1)->toBeTrue();
        expect($called2)->toBeTrue();
    }

    public function testHandleNoTypeHandler()
    {
        $handler = new CallableHandler();
        $called1 = false;
        $called2 = false;

        $handler->setHandler('type1', function() use(&$called1) {
            $called1 = true;
        });
        $handler->setHandler('type2', function () use(&$called2) {
            $called2 = true;
        });

        expect($called1)->toBeFalse();
        expect($called2)->toBeFalse();

        $this->expectException(\Exception::class);
        $handler->handle(ArrayMessage::create('type3', []));
    }

    public function testHandleDefault()
    {
        $handler = new CallableHandler();
        $called1 = false;
        $called2 = false;

        $handler->setHandler('type1', function() use(&$called1) {
            $called1 = true;
        });
        $handler->setHandler('type2', function () use(&$called2) {
            $called2 = true;
        });

        $default = false;
        $handler->setDefaultHandler(function() use(&$default) {
            $default = true;
        });

        expect($called1)->toBeFalse();
        expect($called2)->toBeFalse();

        $handler->handle(ArrayMessage::create('type3', []));

        expect($called1)->toBeFalse();
        expect($called2)->toBeFalse();
        expect($default)->toBeTrue();
    }
}