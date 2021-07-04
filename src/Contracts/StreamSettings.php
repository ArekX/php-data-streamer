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
 * Interface StreamSettings
 * @package ArekX\DataStreamer\Contracts
 *
 * Represents specific stream settings.
 */
interface StreamSettings
{
    /**
     * Returns a stream name
     * @return string
     */
    public function getStreamName(): string;

    /**
     * Returns a consumer group in use.
     * @return string
     */
    public function getConsumerGroup(): string;

    /**
     * Returns a consumer name in the group.
     * @return string
     */
    public function getConsumerName(): string;

    /**
     * Returns whether or not StreamReader should read from the
     * start before switching to the latest messages.
     * @return bool
     */
    public function shouldReadFromStart(): bool;

    /**
     * Returns a setting how many messages to read.
     *
     * @return int
     */
    public function getMessageReadCount(): int;

    /**
     * Return a wait timeout how long to wait for new
     * messages.
     *
     * @return int
     */
    public function getMessageWaitTimeout(): int;
}