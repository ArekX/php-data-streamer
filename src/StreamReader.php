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

    public function run()
    {
        $from = $this->settings->shouldReadFromStart()
            ? Driver::FROM_START
            : Driver::FROM_LATEST;

        $streamName = $this->settings->getStreamName();
        $consumerName = $this->settings->getConsumerName();
        $consumerGroup = $this->settings->getConsumerGroup();
        $amountOfMessages = $this->settings->getMessagesPerRead();
        $waitTimeout = $this->settings->getMessageWaitTimeout();

        $this->driver->createGroup($streamName, $consumerGroup);

        if ($this->failHandler) {
            $this->failHandler->beginHandler();
        }

        while (true) {
            $messages = $this->driver->readGroup(
                $consumerGroup,
                $consumerName,
                $streamName,
                $from,
                $amountOfMessages,
                $waitTimeout
            );

            if (empty($messages)) {
                if ($from === Driver::FROM_START) {
                    $from = Driver::FROM_LATEST;
                }
                continue;
            }

            $handledIds = [];
            $failedMessages = [];

            foreach ($messages as $id => $rawMessage) {
                try {
                    $message = $this->parser->parse($id, $rawMessage);
                    if ($this->handler->handle($message)) {
                        $handledIds[] = $id;
                    }
                } catch (\Exception $e) {
                    if ($this->failHandler) {
                        $failedMessages[] = [
                            'id' => $id,
                            'raw' => $rawMessage,
                            'error' => $e
                        ];
                    } else {
                        throw $e;
                    }
                }
            }

            $this->driver->acknowledge(
                $this->settings->getStreamName(),
                $this->settings->getConsumerGroup(),
                $handledIds
            );

            if ($this->failHandler && !empty($failedMessages)) {
                $this->failHandler->handle($failedMessages);
            }
        }
    }
}