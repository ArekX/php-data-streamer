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

namespace ArekX\DataStreamer\Data;



use ArekX\DataStreamer\Contracts\Message;
use ArekX\DataStreamer\Contracts\MessageHandler;
use Exception;

class CallableHandler implements MessageHandler
{
    protected $handlers = [];
    protected $defaultHandler;

    public function setHandler(string $type, callable $handler)
    {
        $this->handlers[$type] = $handler;
    }

    public function setDefaultHandler(callable $handler)
    {
        $this->defaultHandler = $handler;
    }

    public function handle(Message $message): bool
    {
        $handleMessage = $this->defaultHandler;

        if (!empty($this->handlers[$message->getType()])) {
            $handleMessage = $this->handlers[$message->getType()];
        }

        if (!is_callable($handleMessage)) {
            throw new Exception('Cannot handle message: ' . ($message->getId() ?: 'Unknown ID'));
        }

        return $handleMessage($message);
    }
}