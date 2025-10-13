<?php

declare(strict_types=1);

namespace DNS\Harvester;

use InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class RealDnsResolver implements DnsResolverInterface
{
    public function lookup(string $hostname, int $type): array|false
    {
        return dns_get_record($hostname, $type);
    }

    public function lookupWithRetry(string $hostname, int $recordType, int $maxAttempts = 5): array|false
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $result = $this->lookup($hostname, $recordType);
            if ($result !== false) {
                return $result;
            }
            if ($attempt < $maxAttempts) {
                usleep(500000);
            }
        }

        // something wrong with the record
        // or dns resolver is blocking us
        echo("cannot get hostname {$hostname} with {$recordType}" . PHP_EOL);
        return false;
    }
}

class DNS
{
    private string $domain;
    private RecordList $recordList;
    private bool $showProgress;
    private DnsResolverInterface $resolver;
    public array $results = [];

    public function __construct(string $domain, RecordList $records, bool $showProgress = false)
    {
        $this->domain = $domain;
        $this->recordList = $records;
        $this->showProgress = $showProgress;
        $this->resolver = new RealDnsResolver();
    }

    public function harvest()
    {
        $recordCount = $this->recordList->countTypes();
        $this->getRecords($recordCount);
    }

    public function setDNSResolver(DnsResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getRecords(int $recordCount)
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
            $result = $this->resolver->lookupWithRetry($task['hostname'], $task['recordType']->toDNS(), 5);
            foreach ($result as $records) {
                if (str_contains($records['host'], $this->domain)) {
                    $letter = $task['recordType']->toLetter();
                    $this->results[$letter][] = $records;
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
