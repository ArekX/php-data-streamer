<?php
/**
 * Copyright Aleksandar Panic
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/


namespace ArekX\DataStreamer\Drivers;


use ArekX\DataStreamer\Contracts\Driver;
use Predis\Client;

/**
 * Class PredisDriver
 * @package ArekX\DataStreamer\Drivers
 *
 * Represents an implementation using
 * pure PHP redis driver.
 *
 * @codeCoverageIgnore
 */
class PredisDriver implements Driver
{
    /**
     * Predis Client
     * @var Client
     */
    protected Client $client;

    /**
     * Returns an active client.
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function connect(array $config): void
    {
        $this->client = new Client($config['parameters'] ?? null, $config['options'] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function createGroup(string $streamName, string $consumerGroup): void
    {
        $this->client->executeRaw(['XGROUP', 'CREATE', $streamName, $consumerGroup, '$', 'MKSTREAM']);
    }

    /**
     * @inheritDoc
     */
    public function readMessages(string $consumerGroup, string $consumer, string $stream, string $fromId = self::FROM_START, int $count = 1, int $waitFor = 0): array
    {
        $response = $this->client->executeRaw(['XREADGROUP', 'GROUP', $consumerGroup, $consumer, 'COUNT', $count, 'BLOCK', $waitFor, 'STREAMS', $stream, $fromId]);

        if (empty($response) || empty($response[0])) {
            return [];
        }

        $data = $response[0][1];

        $messages = [];

        foreach ($data as $item) {
            $message = [];
            $max = count($item[1]);
            for($i = 1; $i < $max; $i += 2) {
                $message[$item[1][$i - 1]] = $item[1][$i];
            }

            $messages[$item[0]] = $message;
        }

        return $messages;
    }

    /**
     * @inheritDoc
     */
    public function acknowledge(string $stream, string $consumerGroup, array $ids): void
    {
        $this->client->executeRaw(['XACK', $stream, $consumerGroup, ...$ids]);
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(string $stream, array $message): void
    {
        $command = ['XADD', $stream, '*'];

        foreach ($message as $key => $value) {
            $command[] = $key;
            $command[] = $value;
        }

       $this->client->executeRaw($command);
    }
}