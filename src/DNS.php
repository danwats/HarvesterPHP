<?php

declare(strict_types=1);

namespace DNS\Harvester;

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

    private function checkWildcardDuplicate(array $records, array $wildcard): bool
    {

        if (!isset($wildcard[strtolower($records['type'])])) {
            return false;
        }
        $checks = $wildcard[strtolower($records['type'])];
        if (!empty($checks)) {
            foreach ($checks as $check) {
                switch (RecordType::fromString($records['type'])) {
                    case RecordType::A:
                        if ($check['ip'] === $records['ip']) {
                            return true;
                        }
                        break;
                    case RecordType::AAAA:
                        if ($check['ipv6'] === $records['ipv6']) {
                            return true;
                        }
                        break;
                    case RecordType::CNAME:
                        if ($check['target'] === $records['target']) {
                            return true;
                        }
                        break;
                }
            }
        }
        return false;
    }

    private function checkWildcard(string $domain): array
    {
        $wildcard['a'] = $this->resolver->lookup('wildcardcheck999.' . $domain, RecordType::A->toDNS());
        $wildcard['aaaa'] = $this->resolver->lookup('wildcardcheck999.' . $domain, RecordType::AAAA->toDNS());
        $wildcard['cname'] = $this->resolver->lookup('wildcardcheck999.' . $domain, RecordType::CNAME->toDNS());

        // add in wildcard records
        foreach ($wildcard as $t => $records) {
            if (empty($records)) {
                continue;
            }
            foreach ($records as $record) {
                $record['host'] = "*." . $domain;
                $this->results[$t][] = $record;
            }
        }
        return $wildcard;
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

        $wildcard = $this->checkWildcard($domain);

        if ($this->showProgress) {
            $output = new ConsoleOutput();
            $progressBar = new ProgressBar($output, $recordCount);
        }

        foreach ($tasks as $task) {
            $result = $this->resolver->lookupWithRetry($task['hostname'], $task['recordType']->toDNS(), 5);
            foreach ($result as $records) {
                if (str_contains($records['host'], $this->domain)) {
                    // check for wildcard duplicates
                    $isWildcard = $this->checkWildcardDuplicate($records, $wildcard);
                    if (!$isWildcard) {
                        $letter = $task['recordType']->toLetter();
                        $this->results[$letter][] = $records;
                    }
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

    public function bind($showTTL = true)
    {
        // print the soa first
        if (isset($this->results['soa'])) {
            $r = $this->results['soa'][0];
            if ($r['host'] === $this->domain) {
                $r['host'] = "@";
            } else {
                $r['host'] = $r['host'] . '.';
            }
            echo PHP_EOL;
            echo("{$r['host']} ");
            if ($showTTL) {
                echo("{$r['ttl']} ");
            }
            echo("{$r['class']} ");
            echo("{$r['type']} ");
            echo "{$r['mname']}. ";
            echo "{$r['rname']}. (" . PHP_EOL;
            echo "\t{$r['serial']} ; Serial" . PHP_EOL;
            echo "\t{$r['refresh']} ; Refresh" . PHP_EOL;
            echo "\t{$r['retry']} ; Retry" . PHP_EOL;
            echo "\t{$r['expire']} ; Expire" . PHP_EOL;
            echo "\t{$r['minimum-ttl']} ; Minimum-TTL" . PHP_EOL;
            echo ")";
            echo PHP_EOL;
        }
        foreach ($this->results as $types) {
            foreach ($types as $records) {
                if (RecordType::fromString($records['type']) === RecordType::SOA) {
                    continue;
                }
                if ($records['host'] === $this->domain) {
                    $records['host'] = "@";
                } else {
                    $records['host'] = str_replace('.' . $this->domain, '', $records['host']);
                }
                echo("{$records['host']} ");
                if ($showTTL) {
                    echo("{$records['ttl']} ");
                }
                echo("{$records['class']} ");
                echo("{$records['type']} ");
                switch (RecordType::fromString($records['type'])) {
                    case RecordType::A:
                        echo("{$records['ip']}");
                        break;
                    case RecordType::AAAA:
                        echo("{$records['ipv6']}");
                        break;
                    case RecordType::MX:
                        echo("{$records['pri']} ");
                        echo("{$records['target']}.");
                        break;
                    case RecordType::TXT:
                        echo("\"{$records['txt']}\"");
                        break;
                    case RecordType::CNAME:
                        echo("{$records['target']}.");
                        break;
                    case RecordType::NS:
                        echo("{$records['target']}.");
                        break;
                    default:
                        throw new \ValueError("Missing value type {$records['type']}");

                }
                echo PHP_EOL;
            }
        }
    }
}
