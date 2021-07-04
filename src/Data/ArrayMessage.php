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

class ArrayMessage implements Message
{
    public ?string $id = null;
    public string $type = '';
    public array $payload = [];

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public static function create(string $type, array $payload, $id = null): ArrayMessage
    {
        $message = new ArrayMessage();
        $message->payload = $payload;
        $message->id = $id;
        $message->type = $type;

        return $message;
    }
}