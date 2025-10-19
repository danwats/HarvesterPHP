<?php

declare(strict_types=1);

namespace DNS\Harvester;

enum RecordType: string
{
    case A = 'a';
    case AAAA = 'aaaa';
    case CNAME = 'cname';
    case MX = 'mx';
    case TXT = 'txt';
    case NS = 'ns';
    case SOA = 'soa';
    case SRV = 'srv';

    public function toLetter(bool $caps = false): string
    {
        return $caps ? strtoupper($this->value) : $this->value;
    }

    public static function fromString(string $value): self
    {
        return self::from(strtolower($value));
    }

    public function toDNS(): int
    {
        return match($this->value) {
            'a' => DNS_A,
            'soa' => DNS_SOA,
            'aaaa' => DNS_AAAA,
            'cname' => DNS_CNAME,
            'txt' => DNS_TXT,
            'mx' => DNS_MX,
            'ns' => DNS_NS,
            'srv' => DNS_SRV,
            default => throw new \ValueError("Invalid record type: $this->value")
        };
    }
}
