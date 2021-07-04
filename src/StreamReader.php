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

namespace ArekX\DataStreamer;


use ArekX\DataStreamer\Contracts\Driver;
use ArekX\DataStreamer\Contracts\FailHandler;
use ArekX\DataStreamer\Contracts\MessageHandler;
use ArekX\DataStreamer\Contracts\MessageParser;
use ArekX\DataStreamer\Contracts\StreamSettings;

class StreamReader
{
    protected Driver $driver;
    protected MessageParser $parser;
    protected MessageHandler $handler;
    protected StreamSettings $settings;
    protected ?FailHandler $failHandler = null;

    protected $readFrom = Driver::FROM_START;

    protected $failedMessages = [];

    public function __construct(
        Driver $driver,
        MessageParser $parser,
        MessageHandler $handler,
        StreamSettings $settings,
        FailHandler $failHandler = null
    )
    {
        $this->driver = $driver;
        $this->parser = $parser;
        $this->handler = $handler;
        $this->settings = $settings;
        $this->failHandler = $failHandler;
    }

    /**
     * @codeCoverageIgnore
     */
    public function runLoop()
    {
        $this->initializeStream();

        while (true) {
            $this->processPendingMessages();
        }
    }

    public function getReadFrom(): string
    {
        return $this->readFrom;
    }

    public function processPendingMessages()
    {
        $this->failedMessages = [];

        $messages = $this->readMessages();

        if (empty($messages)) {
            if ($this->readFrom === Driver::FROM_START) {
                $this->readFrom = Driver::FROM_LATEST;
            }
            return;
        }

        $handledIds = [];

        foreach ($messages as $id => $rawMessage) {
            if ($this->processMessage($id, $rawMessage)) {
                $handledIds[] = $id;
            }
        }

        $this->driver->acknowledge(
            $this->settings->getStreamName(),
            $this->settings->getConsumerGroup(),
            $handledIds
        );

        if ($this->failHandler && !empty($this->failedMessages)) {
            $this->failHandler->handle($this->failedMessages);
        }
    }

    protected function processMessage($id, $rawMessage): bool
    {
        try {
            $this->handler->handle($this->parser->parse($id, $rawMessage));
            return true;
        } catch (\Exception $e) {
            if ($this->failHandler) {
                $this->pushErrorMessage($id, $rawMessage, $e);
                return false;
            }

            throw $e;
        }
    }

    protected function pushErrorMessage($id, $rawMessage, ?\Exception $exception = null)
    {
        $this->failedMessages[] = [
            'id' => $id,
            'raw' => $rawMessage,
            'error' => $exception
        ];
    }

    protected function readMessages(): array
    {
        return $this->driver->readMessages(
            $this->settings->getConsumerGroup(),
            $this->settings->getConsumerName(),
            $this->settings->getStreamName(),
            $this->readFrom,
            $this->settings->getMessageReadCount(),
            $this->settings->getMessageWaitTimeout()
        );
    }

    public function initializeStream(): void
    {
        $this->driver->createGroup($this->settings->getStreamName(), $this->settings->getConsumerGroup());
    }
}