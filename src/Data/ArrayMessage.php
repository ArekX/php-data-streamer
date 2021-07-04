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

/**
 * Class ArrayMessage
 * @package ArekX\DataStreamer\Data
 *
 * Represents a generic array message
 * for handling any type of the payload.
 */
class ArrayMessage implements Message
{
    /**
     * ID of the message.
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Type of th message.
     * @var string
     */
    public string $type = '';

    /**
     * Payload of the message.
     * @var array
     */
    public array $payload = [];

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type, array $payload, ?string $id = null): ArrayMessage
    {
        $message = new ArrayMessage();
        $message->payload = $payload;
        $message->id = $id;
        $message->type = $type;

        return $message;
    }
}