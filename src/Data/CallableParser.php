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
use ArekX\DataStreamer\Contracts\MessageParser;
use Exception;

/**
 * Class CallableParser
 * @package ArekX\DataStreamer\Data
 *
 * Represents a callable handler which
 * resolves message data by type to a specific
 * callable function which will return an instance of
 * Message
 *
 * @see Message
 */
class CallableParser implements MessageParser
{
    /**
     * Registered builders by type.
     * Type is the key and callable is the value.
     *
     * @var callable[]
     */
    protected $builders = [];

    /**
     * Represents a default callable builder
     * when no type is registered.
     *
     * If this is not set and no type is found
     * this class will throw an exception.
     *
     * @see CallableParser::parse()
     * @var callable
     */
    protected $defaultBuilder = null;

    /**
     *
     * Sets a callable which should return an instance of
     * Message.
     *
     * Callable should be in the format:
     * ```php
     * function(string $id, string $type, array $payload) {
     *    return // Should return an instance of Message.
     * }
     * ```
     *
     * @param string $type
     * @param callable $builder
     */
    public function setBuilder(string $type, callable $builder)
    {
        $this->builders[$type] = $builder;
    }

    /**
     *
     * Sets a callable which will run if parser cannot
     * resolve to a specific type.
     *
     * Callable should be in the format:
     * ```php
     * function(string $id, string $type, array $payload) {
     *    return // Should return an instance of Message.
     * }
     * ```
     *
     * @param string $type
     * @param callable $builder
     */
    public function setDefaultBuilder(callable $builder)
    {
        $this->defaultBuilder = $builder;
    }

    /**
     * Resolves a message to a specific builder based on the
     * message type.
     *
     * @param string $id ID of the message
     * @param array $message Data of the message
     * @return Message Resolved instance of Message
     * @throws Exception Exception which will be thrown if there is no default builder set and type is not resolved.
     */
    public function parse(string $id, array $message): Message
    {
        $buildMessage = $this->defaultBuilder;
        $type = $message['type'];

        if (!empty($this->builders[$type])) {
            $buildMessage = $this->builders[$type];
        }

        if (!is_callable($buildMessage)) {
            throw new Exception('Could not build message ID:' . $id);
        }

        $payload = @json_decode($message['payload'], true) ?: [];
        return $buildMessage($id, $type, $payload);
    }
}