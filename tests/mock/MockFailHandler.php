<?php


namespace mock;

use ArekX\DataStreamer\Contracts\FailHandler;

class MockFailHandler implements FailHandler
{
    public $failedItems = [];

    public function handle(array $failedItems)
    {
        $this->failedItems[] = $failedItems;
    }
}