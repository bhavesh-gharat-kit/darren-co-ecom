<?php

namespace Medoo\Tests;

use Medoo\Medoo;

#[\PHPUnit\Framework\Attributes\CoversClass(\Medoo\Medoo::class)]
class ColumnMappingTest extends MedooTestCase
{
    public function testColumnHelpersKeepIndexedNestedResultsConsistent(): void
    {
        $database = new class () extends Medoo {
            public function __construct()
            {
                parent::__construct([
                    'testMode' => true
                ]);
            }

            public function pushColumns(array $columns): string
            {
                $map = [];

                return $this->columnPush($columns, $map, true);
            }

            public function buildColumnMap(array $columns): array
            {
                $stack = [];

                return $this->columnMap($columns, $stack, true);
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
                'name (nickname) [String]',
                'age [Int]',
                'active [Bool]',
                'profile [JSON]'
            ]
        ];

        $columnMap = $database->buildColumnMap($columns);

        $this->assertSame(
            '"id","name" AS "nickname","age","active","profile"',
            $database->pushColumns($columns)
        );

        $this->assertSame(
            [
                'id' => ['id', 'String'],
                'name (nickname) [String]' => ['nickname', 'String'],
                'age [Int]' => ['age', 'Int'],
                'active [Bool]' => ['active', 'Bool'],
                'profile [JSON]' => ['profile', 'JSON']
            ],
            $columnMap
        );

        $this->assertSame(
            [
                7 => [
                    'nickname' => 'alice',
                    'age' => 31,
                    'active' => true,
                    'profile' => ['role' => 'admin']
                ],
                9 => [
                    'nickname' => 'bob',
                    'age' => 28,
                    'active' => false,
                    'profile' => ['role' => 'editor']
                ]
            ],
            $database->mapRows(
                [
                    [
                        'id' => 7,
                        'nickname' => 'alice',
                        'age' => '31',
                        'active' => '1',
                        'profile' => '{"role":"admin"}'
                    ],
                    [
                        'id' => 9,
                        'nickname' => 'bob',
                        'age' => '28',
                        'active' => '0',
                        'profile' => '{"role":"editor"}'
                    ]
                ],
                $columns,
                $columnMap
            )
        );
    }
}
