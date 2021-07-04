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

/**
 * Class RedisDriver
 * @package ArekX\DataStreamer\Drivers
 *
 * @codeCoverageIgnore
 */
class RedisDriver implements Driver
{
    /**
     * Represents an instance to native redis PHP extension
     *
     * @var \Redis
     */
    protected \Redis $redis;

    /**
     * RedisDriver constructor.
     */
    public function __construct()
    {
        $this->redis = new \Redis();
    }

    public function getClient(): \Redis
    {
        return $this->redis;
    }

    /**
     * @inheritDoc
     */
    public function connect(array $config): void
    {
        $this->redis->connect(
            $config['host'],
            $config['port'] ?? 6379,
            $config['timeout'] ?? 0.0,
            $config['reserved'] ?? null,
            $config['retryInterval'] ?? 0,
            $config['readTimeout'] ?? 0.0
        );
    }

    /**
     * @inheritDoc
     */
    public function createGroup(string $streamName, string $consumerGroup): void
    {
        $this->redis->xGroup('CREATE', $streamName, $consumerGroup, '$', true);
    }

    /**
     * @inheritDoc
     */
    public function acknowledge(string $stream, string $consumerGroup, array $ids): void
    {
        $this->redis->xAck($stream, $consumerGroup, $ids);
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(string $stream, array $message): void
    {
        $this->redis->xAdd($stream, '*', $message);
    }

    /**
     * @inheritDoc
     */
    public function readMessages(string $consumerGroup, string $consumer, string $stream, string $fromId = self::FROM_START, int $count = 1, int $waitFor = 0): array
    {
        $data = $this->redis->xReadGroup($consumerGroup, $consumer, [$stream => $fromId], $count, $waitFor);
        if (empty($data)) {
            return [];
        }
        return $data[$stream];
    }
}