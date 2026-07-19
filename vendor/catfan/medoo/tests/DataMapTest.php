<?php

namespace Medoo\Tests;

use Medoo\Medoo;

#[\PHPUnit\Framework\Attributes\CoversClass(\Medoo\Medoo::class)]
class DataMapTest extends MedooTestCase
{
    public function testDataMapKeepsIndexedNestedResultConsistent(): void
    {
        $database = new class () extends Medoo {
            public function __construct()
            {
                parent::__construct([
                    'testMode' => true
                ]);
            }

            public function buildColumnMap(array $columns): array
            {
                $stack = [];

                $this->columnMap($columns, $stack, true);

                return $stack;
            }

            public function mapRows(array $rows, array $columns, array $columnMap): array
            {
                $result = [];

                foreach ($rows as $row) {
                    $stack = [];
                    $this->dataMap($row, $columns, $columnMap, $stack, true, $result);
                }

                return $result;
            }
        };

        $columns = [
            'id' => [
                'name [String]',
                'age [Int]',
                'active [Bool]',
                'meta [JSON]'
            ]
        ];

        $rows = [
            [
                'id' => 7,
                'name' => 'alice',
                'age' => '31',
                'active' => '1',
                'meta' => '{"role":"admin"}'
            ],
            [
                'id' => 9,
                'name' => 'bob',
                'age' => '28',
                'active' => '0',
                'meta' => '{"role":"editor"}'
            ]
        ];

        $this->assertSame(
            [
                7 => [
                    'name' => 'alice',
                    'age' => 31,
                    'active' => true,
                    'meta' => ['role' => 'admin']
                ],
                9 => [
                    'name' => 'bob',
                    'age' => 28,
                    'active' => false,
                    'meta' => ['role' => 'editor']
                ]
            ],
            $database->mapRows($rows, $columns, $database->buildColumnMap($columns))
        );
    }
}
