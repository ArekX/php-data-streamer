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
 * Interface MessageConverter
 * @package ArekX\DataStreamer\Contracts
 *
 * Represents a message converter for converting messages
 * into a format for sending to a stream.
 */
interface MessageConverter
{
    /**
     * Converts the message to array format for sending.
     *
     * @param Message $message Message to be sent.
     * @return array
     */
    public function convert(Message $message): array;
}