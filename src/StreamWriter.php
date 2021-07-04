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
use ArekX\DataStreamer\Contracts\Message;
use ArekX\DataStreamer\Contracts\MessageConverter;
use ArekX\DataStreamer\Contracts\StreamSettings;

class StreamWriter
{
    protected Driver $driver;
    protected StreamSettings $settings;
    protected MessageConverter $converter;

    public function __construct(
        Driver $driver,
        StreamSettings $settings,
        MessageConverter $converter
    )
    {
        $this->driver = $driver;
        $this->settings = $settings;
        $this->converter = $converter;
    }

    public function write(Message $message): void
    {
        $this->driver->addToStream(
            $this->settings->getStreamName(),
            $this->converter->convert($message)
        );
    }
}