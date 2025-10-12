<?php

declare(strict_types=1);

namespace DNS\Harvester;

// Template to use for Record
class Record
{
    public function __construct(
        public string $name,
        public array $type,
    ) {
        foreach ($type as $item) {
            if (!$item instanceof RecordType) {
                throw new InvalidArgumentException('All items in $type must be RecordType');
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->type;
    }
}
