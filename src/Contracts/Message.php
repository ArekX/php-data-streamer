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
 * Interface Message
 * @package ArekX\DataStreamer\Contracts
 *
 * Represents a single message in the system.
 */
interface Message
{
    /**
     * Unique ID of the message or null if not present.
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Type of the message.
     * @return string
     */
    public function getType(): string;

    /**
     * Returns payload of the message.
     * @return array
     */
    public function getPayload(): array;

    /**
     * Creates a message instance from data.
     *
     * @param string $type Type of the message.
     * @param array $payload Payload of the message
     * @param string|null $id ID of the message
     * @return static
     */
    public static function create(string $type, array $payload, ?string $id = null): self;
}