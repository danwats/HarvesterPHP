<?php

use DNS\Harvester\DNS;
use DNS\Harvester\RecordList;
use DNS\Harvester\RecordType;
use DNS\Harvester\Record;
use DNS\Harvester\DnsResolverInterface;


test('check record list with some defaults', function() {
    $recordList = new RecordList();
    $recordList->loadDefaults();

    expect($recordList->records[0]->getName())->toEqual('@');
    expect($recordList->records[1]->getName())->toEqual('www');
    expect($recordList->records[0]->getValues())->toEqual([RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT, RecordType::MX, RecordType::NS, RecordType::SOA]);
    expect($recordList->records[1]->getValues())->toEqual([RecordType::A, RecordType::AAAA, RecordType::CNAME, RecordType::TXT, RecordType::MX]);
});

test('check recordtype a record to letter uppercase with success', function() {
    $r = RecordType::A;
    expect($r)->toEqual(RecordType::A);
    expect($r)->toLetter()->toEqual('a');
});

test('check recordtype a record to letter lowercase with success', function() {
    $r = RecordType::A;
    expect($r)->toEqual(RecordType::A);
    expect($r)->toLetter(true)->toEqual('A');
});

test('check recordtype a record with success', function() {
    $r = RecordType::A;
    expect($r)->toEqual(RecordType::A);
});

test('check recordtype to dns with success', function() {
    $r = RecordType::A;
    expect($r)->toEqual(RecordType::A);
    expect($r->toDNS())->toEqual(DNS_A);

    $r = RecordType::AAAA;
    expect($r)->toEqual(RecordType::AAAA);
    expect($r->toDNS())->toEqual(DNS_AAAA);

    $r = RecordType::CNAME;
    expect($r)->toEqual(RecordType::CNAME);
    expect($r->toDNS())->toEqual(DNS_CNAME);

    $r = RecordType::MX;
    expect($r)->toEqual(RecordType::MX);
    expect($r->toDNS())->toEqual(DNS_MX);

    $r = RecordType::TXT;
    expect($r)->toEqual(RecordType::TXT);
    expect($r->toDNS())->toEqual(DNS_TXT);

    $r = RecordType::NS;
    expect($r)->toEqual(RecordType::NS);
    expect($r->toDNS())->toEqual(DNS_NS);

    $r = RecordType::SOA;
    expect($r)->toEqual(RecordType::SOA);
    expect($r->toDNS())->toEqual(DNS_SOA);

    $r = RecordType::SRV;
    expect($r)->toEqual(RecordType::SRV);
    expect($r->toDNS())->toEqual(DNS_SRV);
});

test('check recordtype fromString a record with success', function() {
    $r = RecordType::fromString('a');
    expect($r)->toEqual(RecordType::A);
});

test('check record type a with success', function() {
    $r = new Record('www', [RecordType::A]);

    expect($r->getName())->toEqual('www');
    expect($r->getValues()[0])->toEqual(RecordType::A);
    
});

test('check record type a, cname and mx with success', function() {
    $tests = ['expected' =>[RecordType::A, RecordType::CNAME,RecordType::MX]];
    $r = new Record('www', [RecordType::A, RecordType::CNAME, RecordType::MX]);

    expect($r->getName())->toEqual('www');
    expect($r->getValues())->toEqual($tests['expected']);
});

test('check record type with invalid record type', function() {
    expect(fn() => new Record('www', ["a"]))->toThrow(\TypeError::class);
});

test('check record list with www hostname and a record type', function() {
    $recordList = new RecordList();
    $recordList->add(new Record('www', [RecordType::A]));

    expect($recordList->records[0]->getName())->toEqual('www');
    expect($recordList->records[0]->getValues()[0])->toEqual(RecordType::A);
});

test('check record list with www hostname and a record type and count', function() {
    $recordList = new RecordList();
    $recordList->add(new Record('www', [RecordType::A]));

    expect($recordList->records[0]->getName())->toEqual('www');
    expect($recordList->records[0]->getValues()[0])->toEqual(RecordType::A);
    expect($recordList->countTypes())->toEqual(1);
});

test('check record list with www hostname and invalid record type', function() {
    $recordList = new RecordList();
    expect(fn() => $recordList->add(new Record('www', ['a'])))->toThrow(\TypeError::class);
});


test('get a record', function () {
    $tests = [
        [
            'test' => [
                'domain' => 'example.com',
                'hostname' => 'www',
                'record_type' => RecordType::A,
                'host' => 'www.example.com',
                'count' => 1,
            ],
            'expected' => [
                [
                    'host' => 'www.example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'A',
                    'ip' => '10.10.10.10',
                ]
            ],
        ],
        [
            'test' => [
                'domain' => 'example.com',
                'hostname' => '@',
                'record_type' => RecordType::A,
                'host' => 'example.com',
                'class' => 'IN',
                'count' => 2,
            ],
            'expected' => [
                [
                    'host' => 'example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'A',
                    'ip' => '10.10.10.10',
                ],
                [
                    'host' => 'example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'A',
                    'ip' => '10.10.10.11',
                ]
            ],
            'json' => [
                'a' => [
                    [
                        "host" => "example.com",
                        "ttl" => 300,
                        "class" => "IN",
                        "type" => "A",
                        "ip" => "10.10.10.10"
                    ],
                    [
                        "host" => "example.com",
                        "ttl" => 300,
                        "class" => "IN",
                        "type" => "A",
                        "ip" => "10.10.10.11"
                    ],
                ],
            ]
        ]
    ];

    foreach ($tests as $i => $test) {
        $recordList = new RecordList();
        $recordList->add(new Record($test['test']['hostname'], [$test['test']['record_type']]));


        $mockResolver = $this->createMock(DnsResolverInterface::class);
        $mockResolver->method('lookupWithRetry')->willReturnCallback(function ($domain, $type) use ($test) {
            if (str_contains($domain, "wildcardcheck999")) {
                return [];
            }
            return [
                ...$test['expected'],
            ];
        });


        $dns = new DNS($test['test']['domain'], $recordList);
        $dns->setDNSResolver($mockResolver);
        $dns->harvest();

        expect(count($dns->results['a']))->toEqual($test['test']['count']);
        expect($dns->results)->toBeArray();

        foreach ($test['expected'] as $i => $expected) {
            $record = $dns->results['a'][$i];
            expect($record)->not->toBeNull();
            expect($record['host'])->toEqual($expected['host']);
            expect($record['class'])->toEqual($expected['class']);
            expect($record['ttl'])->toEqual($expected['ttl']);
            expect($record['type'])->toEqual($expected['type']);
            expect($test['test']['record_type']->toLetter(true))->toEqual($expected['type']);
            expect($record['ip'])->toEqual($expected['ip']);
        }
        expect($dns->json(JSON_PRETTY_PRINT))->toBeJson();
        expect($dns->json())->toBeJson();
        if (!empty($test['json'])) {
            expect($dns->json(JSON_PRETTY_PRINT))->toEqual(json_encode($test['json'], JSON_PRETTY_PRINT));
        }
    }
});

test('get cname record', function () {
    $tests = [
        [
            'test' => [
                'domain' => 'example.com',
                'hostname' => 'www',
                'record_type' => RecordType::CNAME,
                'host' => 'www.example.com',
                'class' => 'IN',
                'count' => 1,
            ],
            'expected' => [
                [
                    'host' => 'www.example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'CNAME',
                    'target' => 'something.example.com',
                ]
            ],
        ],
    ];

    foreach ($tests as $i => $test) {
        $recordList = new RecordList();
        $recordList->add(new Record($test['test']['hostname'], [$test['test']['record_type']]));


        $mockResolver = $this->createMock(DnsResolverInterface::class);
        $mockResolver->method('lookupWithRetry')->willReturnCallback(function ($domain, $type) use ($test) {
            if (str_contains($domain, "wildcardcheck999")) {
                return [];
            }
            return [
                ...$test['expected'],
            ];
        });


        $dns = new DNS($test['test']['domain'], $recordList);
        $dns->setDNSResolver($mockResolver);
        $dns->harvest();

        expect(count($dns->results['cname']))->toEqual($test['test']['count']);
        expect($dns->results)->toBeArray();

        foreach ($test['expected'] as $i => $expected) {
            $record = $dns->results['cname'][$i];
            expect($record)->not->toBeNull();
            expect($record['host'])->toEqual($expected['host']);
            expect($record['class'])->toEqual($expected['class']);
            expect($record['type'])->toEqual($expected['type']);
            expect($test['test']['record_type']->toLetter(true))->toEqual($expected['type']);
            expect($record['ttl'])->toEqual($expected['ttl']);
            expect($record['target'])->toEqual($expected['target']);
        }
        expect($dns->json(JSON_PRETTY_PRINT))->toBeJson();
        expect($dns->json())->toBeJson();
        if (!empty($test['json'])) {
            expect($dns->json(JSON_PRETTY_PRINT))->toEqual(json_encode($test['json'], JSON_PRETTY_PRINT));
        }
    }
});

test('check dns with wildcard check', function () {
    $recordList = new RecordList();
    $recordList->add(new Record('@', [RecordType::A, RecordType::AAAA]));

    $mockResolver = $this->createMock(DnsResolverInterface::class);
    $mockResolver->method('lookupWithRetry')->willReturnCallback(function ($domain, $type) {
        if ($type === RecordType::A->toDNS()) {
            // to add to wildcard check
            if (str_contains($domain, "wildcardcheck999")) {
                return [
                    [
                        'host' => $domain,
                        'ttl' => 300,
                        'class' => 'IN',
                        'type' => 'A',
                        'ip' => '10.10.10.10',
                    ]
                ];
            }
            return [
                [
                    'host' => 'example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'A',
                    'ip' => '11.10.10.11',
                ],
                [
                    'host' => 'www.example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'A',
                    'ip' => '10.10.10.10',
                ]
            ];
        }
        if ($type === RecordType::AAAA->toDNS()) {
            // to add to wildcard check
            if (str_contains($domain, "wildcardcheck999")) {
                return [
                    [
                        'host' => $domain,
                        'ttl' => 300,
                        'class' => 'IN',
                        'type' => 'AAAA',
                        'ipv6' => '0:0:0:0:0:0:0:1',
                    ]
                ];
            }
            return [
                [
                    'host' => 'example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'AAAA',
                    'ipv6' => '0:0:0:0:0:0:0:12',
                ],
                [
                    'host' => 'www.example.com',
                    'ttl' => 300,
                    'class' => 'IN',
                    'type' => 'AAAA',
                    'ipv6' => '0:0:0:0:0:0:0:1',
                ]
            ];
        }
        return [];
    });

    $dns = new DNS('example.com', $recordList, true);
    $dns->setDNSResolver($mockResolver);
    $dns->harvest();

    expect($dns->results['a'][0]['host'])->toEqual('*.example.com');
    expect($dns->results['a'][0]['ip'])->toEqual('10.10.10.10');
    expect($dns->results['a'][0]['type'])->toEqual('A');
    expect($dns->results['a'][1]['host'])->toEqual('example.com');
    expect($dns->results['a'][1]['ip'])->toEqual('11.10.10.11');
    expect($dns->results['a'][1]['type'])->toEqual('A');
    expect(count($dns->results['a']))->toEqual(2);
    expect($dns->results['aaaa'][0]['host'])->toEqual('*.example.com');
    expect($dns->results['aaaa'][0]['type'])->toEqual('AAAA');
    expect($dns->results['aaaa'][0]['ipv6'])->toEqual('0:0:0:0:0:0:0:1');
    expect($dns->results['aaaa'][1]['host'])->toEqual('example.com');
    expect($dns->results['aaaa'][1]['type'])->toEqual('AAAA');
    expect($dns->results['aaaa'][1]['ipv6'])->toEqual('0:0:0:0:0:0:0:12');
    expect(count($dns->results['aaaa']))->toEqual(2);

});
