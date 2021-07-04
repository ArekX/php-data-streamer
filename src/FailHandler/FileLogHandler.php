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

namespace ArekX\DataStreamer\FailHandler;


use ArekX\DataStreamer\Contracts\FailHandler;

class FileLogHandler implements FailHandler
{
    protected $logFile;
    protected $path;

    public function __construct(string $logFilePath)
    {
        $this->path = $logFilePath;
        $this->beginHandler();

        register_shutdown_function(function () {
            $this->endHandler();
        });
    }

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

    public function beginHandler(): void
    {
        $this->logFile = fopen($this->path, "a");
    }

    public function endHandler(): void
    {
        if (is_resource($this->logFile)) {
            fclose($this->logFile);
        }
    }
}