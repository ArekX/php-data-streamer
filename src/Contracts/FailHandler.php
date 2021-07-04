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

use ArekX\DataStreamer\StreamReader;

/**
 * Interface FailHandler
 * @package ArekX\DataStreamer\Contracts
 *
 * Represents a fail handler to handle failures
 * when a message handler throws an exception
 *
 * @see MessageHandler
 */
interface FailHandler
{
    /**
     * Handles failed messages which
     * occurred during processing.
     *
     * @see StreamReader::pushErrorMessage()()
     * @param array $failedItems
     * @return mixed
     */
    public function handle(array $failedItems);
}