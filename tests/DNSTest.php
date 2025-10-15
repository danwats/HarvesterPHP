<?php

namespace DNS\Harvester\Tests;

use PHPUnit\Framework\TestCase;
use DNS\Harvester\DNS;
use DNS\Harvester\RecordList;
use DNS\Harvester\RecordType;
use DNS\Harvester\Record;
use DNS\Harvester\DnsResolverInterface;

class DNSTest extends TestCase
{
    public function testGetARecord()
    {
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
            echo "running test {$i}" . PHP_EOL;

            $recordList = new RecordList();
            $recordList->add(new Record($test['test']['hostname'], [$test['test']['record_type']]));


            $mockResolver = $this->createMock(DnsResolverInterface::class);
            $mockResolver->method('lookupWithRetry')->willReturn([
                ...$test['expected'],
            ]);


            $dns = new DNS($test['test']['domain'], $recordList);
            $dns->setDNSResolver($mockResolver);
            $dns->harvest();

            $this->assertEquals($test['test']['count'], count($dns->results['a']));
            $this->assertIsArray($dns->results);

            foreach ($test['expected'] as $i => $expected) {
                $record = $dns->results['a'][$i];
                $this->assertNotNull($record);
                $this->assertEquals($expected['host'], $record['host']);
                $this->assertEquals($expected['class'], $record['class']);
                $this->assertEquals($expected['ttl'], $record['ttl']);
                $this->assertEquals($expected['type'], $record['type']);
                $this->assertEquals($expected['type'], $test['test']['record_type']->toLetter(true));
                $this->assertEquals($expected['ip'], $record['ip']);
            }
            $this->assertJson($dns->json(JSON_PRETTY_PRINT));
            $this->assertJson($dns->json());
            if (!empty($test['json'])) {
                $this->assertEquals(json_encode($test['json'], JSON_PRETTY_PRINT), $dns->json(JSON_PRETTY_PRINT));
            }
        }
    }
    public function testGetCNAMERecord()
    {
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
            echo "running test {$i}" . PHP_EOL;

            $recordList = new RecordList();
            $recordList->add(new Record($test['test']['hostname'], [$test['test']['record_type']]));


            $mockResolver = $this->createMock(DnsResolverInterface::class);
            $mockResolver->method('lookupWithRetry')->willReturn([
                ...$test['expected'],
            ]);


            $dns = new DNS($test['test']['domain'], $recordList);
            $dns->setDNSResolver($mockResolver);
            $dns->harvest();

            $this->assertEquals($test['test']['count'], count($dns->results['cname']));
            $this->assertIsArray($dns->results);

            foreach ($test['expected'] as $i => $expected) {
                $record = $dns->results['cname'][$i];
                $this->assertNotNull($record);
                $this->assertEquals($expected['host'], $record['host']);
                $this->assertEquals($expected['class'], $record['class']);
                $this->assertEquals($expected['type'], $record['type']);
                $this->assertEquals($expected['type'], $test['test']['record_type']->toLetter(true));
                $this->assertEquals($expected['ttl'], $record['ttl']);
                $this->assertEquals($expected['target'], $record['target']);
            }
            $this->assertJson($dns->json(JSON_PRETTY_PRINT));
            $this->assertJson($dns->json());
            if (!empty($test['json'])) {
                $this->assertEquals(json_encode($test['json'], JSON_PRETTY_PRINT), $dns->json_pretty());
            }
        }
    }
}
