<?php

declare(strict_types=1);

namespace DNS\Harvester;

interface DnsResolverInterface
{
    public function lookup(string $hostname, int $type): array|false;
    public function lookupWithRetry(string $hostname, int $type, int $maxAttempts): array|false;
}
