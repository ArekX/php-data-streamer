<?php


namespace mock;

use ArekX\DataStreamer\Contracts\Driver;

class MockDriver implements Driver
{
    public $connected = false;

    public $groupCreated = [];

    public $messages = [];

    public $acknowledged = [];

    public $counter = 0;

    public function reset()
    {
        $this->connected = false;
        $this->groupCreated = [];
        $this->messages = [];
        $this->acknowledged = [];
        $this->counter = 0;
    }

    public function connect(array $config): void
    {
        $this->connected = true;
    }

    public function createGroup(string $streamName, string $consumerGroup): void
    {
        $this->groupCreated[] = [$streamName, $consumerGroup];
    }

    public function readMessages(string $consumerGroup, string $consumer, string $stream, string $fromId = self::FROM_START, int $count = 1, int $waitFor = 0): array
    {
        $results = [];

        for($i = 0; $i < $count; $i++) {
            if (empty($this->messages[$stream])) {
                break;
            }

            $rawMessage = array_shift($this->messages[$stream]);

            if (empty($rawMessage)) {
                break;
            }

            ['id' => $id, 'message' => $message] = $rawMessage;
            $results[$id] = $message;
        }

        return $results;
    }

    public function acknowledge(string $stream, string $consumerGroup, array $ids): void
    {
        $this->acknowledged[$stream][$consumerGroup][] = $ids;
    }

    public function sendMessage(string $stream, array $message): void
    {
        $this->messages[$stream][] = ['id' => time() . '-' . $this->counter++, 'message' => $message];
    }
}