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

use ArekX\DataStreamer\Contracts\Message;
use ArekX\DataStreamer\Contracts\MessageHandler;
use Exception;

/**
 * Class CallableHandler
 * @package ArekX\DataStreamer\Data
 *
 * Represents a callable handler which
 * handles the message.
 *
 * @see Message
 */
class CallableHandler implements MessageHandler
{
    /**
     * Registered handlers by type.
     * Type is the key and callable is the value.
     *
     * @var callable[]
     */
    protected $handlers = [];

    /**
     * Represents a default callable handler
     * when no type is registered.
     * If this is not set and no type is found
     * this class will throw an exception.
     *
     * @see CallableHandler::handle()
     * @var callable
     */
    protected $defaultHandler;

    /**
     * Sets a callable which will handle the message.
     *
     * Callable should be in the format:
     * ```php
     * function(Message $message) {
     * }
     * ```
     *
     * @param string $type
     * @param callable $handler
     */
    public function setHandler(string $type, callable $handler)
    {
        $this->handlers[$type] = $handler;
    }

    /**
     * Sets a default callable which will handle the message if it
     * is not resolved by type.
     *
     * Callable should be in the format:
     * ```php
     * function(Message $message) {
     * }
     * ```
     *
     * @param callable $handler
     */
    public function setDefaultHandler(callable $handler)
    {
        $this->defaultHandler = $handler;
    }

    /**
     * Handles a message by calling a callable
     * from a type or a default handler.
     *
     * @param Message $message Message to be handled
     * @throws Exception Exception which will be thrown if no callable is found and default is not set.
     */
    public function handle(Message $message): void
    {
        $handleMessage = $this->defaultHandler;

        if (!empty($this->handlers[$message->getType()])) {
            $handleMessage = $this->handlers[$message->getType()];
        }

        if (!is_callable($handleMessage)) {
            throw new Exception('Cannot handle message: ' . ($message->getId() ?: 'Unknown ID'));
        }

        $handleMessage($message);
    }
}