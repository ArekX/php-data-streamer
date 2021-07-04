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

namespace ArekX\DataStreamer\Data;

use ArekX\DataStreamer\Contracts\StreamSettings;

/**
 * Class Settings
 * @package ArekX\DataStreamer\Data
 *
 * Represents settings to configure the
 * stream reader or a writer.
 */
class Settings implements StreamSettings
{
    /**
     * Settings to be read.
     * @var array
     */
    protected array $settings;

    /**
     * Settings constructor.
     * @param array $settings Settings which will be read.
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @inheritDoc
     */
    public function getStreamName(): string
    {
        return $this->settings['stream'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getConsumerGroup(): string
    {
        return $this->settings['consumerGroup'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getConsumerName(): string
    {
        return $this->settings['consumerName'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function shouldReadFromStart(): bool
    {
        return $this->settings['readFromStart'] ?? true;
    }

    /**
     * @inheritDoc
     */
    public function getMessageReadCount(): int
    {
        return $this->settings['messagesPerRead'] ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function getMessageWaitTimeout(): int
    {
        return $this->settings['waitTimeout'] ?? 10000;
    }
}