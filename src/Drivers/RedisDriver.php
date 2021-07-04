<?php
/**
 * Copyright 2020 Aleksandar Panic
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

class RedisDriver implements Driver
{
    protected \Redis $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
    }

    public function connect(array $config)
    {
        $this->redis->connect($config['host']);
    }

    public function createGroup(string $streamName, string $consumerGroup): void
    {
        $this->redis->xGroup('CREATE', $streamName, $consumerGroup, '$', true);
    }

    public function acknowledge(string $stream, string $consumerGroup, array $ids): void
    {
        $this->redis->xAck($stream, $consumerGroup, $ids);
    }

    public function addToStream(string $stream, array $message)
    {
        $this->redis->xAdd($stream, '*', $message);
    }

    public function readGroup(string $consumerGroup, string $consumer, string $stream, string $fromId = self::FROM_START, int $count = 1, int $waitFor = 0): array
    {
        $data = $this->redis->xReadGroup($consumerGroup, $consumer, [$stream => $fromId], $count, $waitFor);
        if (empty($data)) {
            return [];
        }
        return $data[$stream];
    }
}