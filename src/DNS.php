<?php

declare(strict_types=1);

namespace DNS\Harvester;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class DNS
{
    private string $domain;
    private RecordList $recordList;
    private bool $showProgress;
    public array $results = [];

    public function __construct(string $domain, RecordList $records, bool $showProgress = false)
    {
        $this->domain = $domain;
        $this->recordList = $records;
        $this->showProgress = $showProgress;
    }

    public function harvest()
    {
        $recordCount = $this->recordList->countTypes();
        $this->getRecords($recordCount);
    }

    private function getRecords(int $recordCount)
    {
        $records = $this->recordList;
        $domain = $this->domain;

        $tasks = [];
        foreach ($records as $recordList) {
            foreach ($recordList as $record) {
                foreach ($record->type as $recordTypes) {
                    if ($record->name === "@") {
                        array_push($tasks, ['hostname' => $domain, 'recordType' => $recordTypes]);
                    } else {
                        array_push($tasks, ['hostname' => $record->name . '.' . $domain, 'recordType' => $recordTypes]);
                    }
                }
            }
        }

        if ($this->showProgress) {
            $output = new ConsoleOutput();
            $progressBar = new ProgressBar($output, $recordCount);
        }
        foreach ($tasks as $task) {
            $result = dns_get_record($task['hostname'], $task['recordType']->toDNS());
            foreach ($result as $records) {
                if (str_contains($records['host'], $this->domain)) {
                    $this->results[$task['recordType']->toLetter()][$task['hostname']] = $result;
                }
            }
            if ($this->showProgress) {
                $progressBar->advance();
            }
        }
    }

    public function json(): string
    {
        return json_encode($this->results);
    }

    public function json_pretty(): string
    {
        return json_encode($this->results, JSON_PRETTY_PRINT);
    }
}
