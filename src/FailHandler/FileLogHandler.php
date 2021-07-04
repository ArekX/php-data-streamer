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

namespace ArekX\DataStreamer\FailHandler;


use ArekX\DataStreamer\Contracts\FailHandler;

/**
 * Class FileLogHandler
 * @package ArekX\DataStreamer\FailHandler
 *
 * @codeCoverageIgnore
 */
class FileLogHandler implements FailHandler
{
    /**
     * Represents a resource to the log file.
     * @var resource
     */
    protected $logFile;

    /**
     * Represents a path where the log file is located.
     * @var string
     */
    protected $path;

    /**
     * FileLogHandler constructor.
     * @param string $logFilePath
     */
    public function __construct(string $logFilePath)
    {
        $this->path = $logFilePath;
        $this->openLogFile();

        register_shutdown_function(function () {
            $this->closeLogFile();
        });
    }

    /**
     * @inheritDoc
     */
    public function handle(array $failedItems)
    {
        if (!is_resource($this->logFile)) {
            return;
        }
        $time = microtime(true);

        $lines = "";
        foreach ($failedItems as $failedItem) {
            $lines .= "[{$time}]: " . json_encode($failedItem) . PHP_EOL;
        }

        fwrite($this->logFile, $lines);
    }

    /**
     * Opens the file at location for writing data.
     */
    public function openLogFile(): void
    {
        $this->logFile = fopen($this->path, "a");
    }

    /**
     * Closes the file and ends writing.
     */
    public function closeLogFile(): void
    {
        if (is_resource($this->logFile)) {
            fclose($this->logFile);
        }
    }
}