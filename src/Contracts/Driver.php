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

namespace ArekX\DataStreamer\Contracts;

/**
 * Interface Driver
 * @package ArekX\DataStreamer\Contracts
 *
 * Represents a driver to handle the data layer
 * which transmits the messages.
 */
interface Driver
{
    const FROM_START = '0';
    const FROM_LATEST = '>';

    /**
     * Performs a connection to the server.
     *
     * @param array $config Configuration for connection to be passed.
     */
    public function connect(array $config): void;

    /**
     * Creates group for consuming messages.
     *
     * @param string $streamName Stream name for the consumer group
     * @param string $consumerGroup Consumer group to consume messages
     */
    public function createGroup(string $streamName, string $consumerGroup): void;

    /**
     * Reads messages for the consumer group.
     *
     * @param string $consumerGroup Consumer group for which to read messages.
     * @param string $consumer Consumer in the group which will read the messages.
     * @param string $stream Stream to read the message from.
     * @param string $fromId ID from which to start to read messages.
     * @param int $count Amount of messages to be read.
     * @param int $waitFor Duration in milliseconds to wait for the messages.
     * @return array Received messages
     */
    public function readMessages(string $consumerGroup, string $consumer, string $stream, string $fromId = self::FROM_START, int $count = 1, int $waitFor = 0): array;

    /**
     * Marks message ids as acknowledged.
     *
     * @param string $stream Stream to acknowledge messages in
     * @param string $consumerGroup Consumer group for which to acknowledge the messages.
     * @param array $ids Message IDs to acknowledge
     */
    public function acknowledge(string $stream, string $consumerGroup, array $ids): void;

    /**
     * Sends a message to the stream.
     *
     * @param string $stream Stream to which to send the message.
     * @param array $message Message to be sent
     */
    public function sendMessage(string $stream, array $message): void;
}