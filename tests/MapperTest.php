<?php

namespace Dalue\Tests;

use Dalue\Mapper;
use PHPUnit\Framework\TestCase;
use StrObj\Behavior;
use StrObj\StringObjects;

final class MapperTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private static function order(): array
    {
        return [
            'transaction_id' => 'tx_987654321',
            'customer' => [
                'account_no' => 'CUST-1002',
                'full_name' => 'John Doe',
            ],
            'purchase_details' => [
                'total_amount' => 150.50,
                'items' => [
                    ['sku' => 'ITEM-01', 'qty' => 2, 'price' => 50.00],
                    ['sku' => 'ITEM-02', 'qty' => 1, 'price' => 50.50],
                ],
            ],
        ];
    }

    public function testMapsDataPathsAndStaticValues(): void
    {
        $data = StringObjects::instance([
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
                'details' => ['age' => 30, 'city' => 'New York'],
            ],
        ]);

        $result = Mapper::map($data, [
            'userID' => '@data/user/id',
            'userName' => '@data/user/name',
            'userDetails' => [
                'userAge' => '@data/user/details/age',
                'userCity' => '@data/user/details/city',
            ],
            'source' => 'api',
            'version' => 2,
        ]);

        $this->assertSame([
            'userID' => 1,
            'userName' => 'John Doe',
            'userDetails' => ['userAge' => 30, 'userCity' => 'New York'],
            'source' => 'api',
            'version' => 2,
        ], $result);
    }

    public function testAcceptsJsonInput(): void
    {
        $data = StringObjects::instance('{"a":{"b":"c"}}');

        $this->assertSame(['value' => 'c'], Mapper::map($data, ['value' => '@data/a/b']));
    }

    public function testReturnsAnEmptyArrayForAnEmptySchema(): void
    {
        $this->assertSame([], Mapper::map(StringObjects::instance([]), []));
    }

    public function testMapsTheReadmeOrderExample(): void
    {
        $result = Mapper::map(StringObjects::instance(self::order()), [
            'PK' => '@data/transaction_id',
            'SK' => 'METADATA',
            'CustomerId' => '@data/customer/account_no',
            'TotalAmount' => '@data/purchase_details/total_amount',
            'OrderItems[]' => [
                'SKU' => '@data/purchase_details/items/*/sku',
                'Quantity' => '@data/purchase_details/items/*/qty',
                'Price' => '@data/purchase_details/items/*/price',
            ],
        ]);

        $this->assertSame([
            'PK' => 'tx_987654321',
            'SK' => 'METADATA',
            'CustomerId' => 'CUST-1002',
            'TotalAmount' => 150.50,
            'OrderItems' => [
                ['SKU' => 'ITEM-01', 'Quantity' => 2, 'Price' => 50.00],
                ['SKU' => 'ITEM-02', 'Quantity' => 1, 'Price' => 50.50],
            ],
        ], $result);
    }

    public function testKeepsFalsyValuesUnchanged(): void
    {
        $data = StringObjects::instance(['f' => false, 'n' => null, 'z' => 0, 'e' => '', 'a' => []]);

        $result = Mapper::map($data, [
            'f' => '@data/f',
            'n' => '@data/n',
            'z' => '@data/z',
            'e' => '@data/e',
            'a' => '@data/a',
        ]);

        $this->assertSame(['f' => false, 'n' => null, 'z' => 0, 'e' => '', 'a' => []], $result);
    }

    public function testTheBarePrefixReturnsTheWholeDocument(): void
    {
        $data = StringObjects::instance(['a' => ['b' => 1]]);

        $this->assertSame(['all' => ['a' => ['b' => 1]]], Mapper::map($data, ['all' => '@data/']));
    }

    public function testStrObjFiltersAreApplied(): void
    {
        $data = StringObjects::instance(['price' => '19.90', 'stock' => '-3'], [
            'filters' => [
                'price' => ['type' => 'float'],
                'stock' => [
                    'type' => 'int',
                    'callback' => static function (int $value): bool {
                        return $value >= 0;
                    },
                ],
            ],
        ]);

        $result = Mapper::map($data, ['price' => '@data/price', 'stock' => '@data/stock'], null, 0);

        $this->assertSame(['price' => 19.9, 'stock' => 0], $result);
    }

    public function testMissingPathsReturnNullByDefault(): void
    {
        $result = Mapper::map(StringObjects::instance(['a' => 1]), [
            'missing' => '@data/nope',
            'deep' => '@data/a/b/c',
        ]);

        $this->assertSame(['missing' => null, 'deep' => null], $result);
    }

    public function testMissingPathsReturnTheGivenDefault(): void
    {
        $data = StringObjects::instance(['f' => false]);

        $result = Mapper::map($data, ['missing' => '@data/nope', 'f' => '@data/f'], null, 'n/a');

        $this->assertSame(['missing' => 'n/a', 'f' => false], $result);
    }

    public function testTheDefaultIsPassedToNestedSchemas(): void
    {
        $result = Mapper::map(StringObjects::instance([]), ['x' => ['y' => '@data/y']], null, '');

        $this->assertSame(['x' => ['y' => '']], $result);
    }

    public function testFalsyKeysDoNotStopMapping(): void
    {
        $data = StringObjects::instance(['a' => 1]);

        $result = Mapper::map($data, [0 => 'zero', '' => 'empty', 'after' => '@data/a', 1 => '@data/a']);

        $this->assertSame([0 => 'zero', '' => 'empty', 'after' => 1, 1 => 1], $result);
    }

    public function testListSchemasKeepTheirOrder(): void
    {
        $data = StringObjects::instance(['a' => 'A', 'b' => 'B']);

        $this->assertSame(['A', 'B', 'C'], Mapper::map($data, ['@data/a', '@data/b', 'C']));
    }

    public function testIgnoresTheInternalArrayPointer(): void
    {
        $schema = ['a' => 1, 'b' => 2];
        next($schema);
        next($schema);

        $this->assertSame(['a' => 1, 'b' => 2], Mapper::map(StringObjects::instance([]), $schema));
    }

    public function testOnlyStringsWithTheDataPrefixAreResolved(): void
    {
        $data = StringObjects::instance(['a' => 'A']);

        $result = Mapper::map($data, [
            'plain' => 'a',
            'inner' => 'x @data/a',
            'upper' => '@DATA/a',
            'null' => null,
            'float' => 1.5,
            'bool' => true,
        ]);

        $this->assertSame([
            'plain' => 'a',
            'inner' => 'x @data/a',
            'upper' => '@DATA/a',
            'null' => null,
            'float' => 1.5,
            'bool' => true,
        ], $result);
    }

    public function testStaticObjectsAreCopiedByReference(): void
    {
        $object = new \stdClass();

        $result = Mapper::map(StringObjects::instance([]), ['o' => $object]);

        $this->assertSame($object, $result['o']);
    }

    public function testWildcardWithoutMatchesReturnsAnEmptyList(): void
    {
        $data = StringObjects::instance(['items' => []]);

        $result = Mapper::map($data, [
            'skus' => '@data/items/*/sku',
            'Rows[]' => ['sku' => '@data/items/*/sku'],
        ]);

        $this->assertSame(['skus' => [], 'Rows' => []], $result);
    }

    public function testCollectionRowsStayAlignedWhenAFieldIsMissing(): void
    {
        $data = StringObjects::instance(['items' => [
            ['sku' => 'A', 'qty' => 1],
            ['qty' => 2],
            ['sku' => 'C', 'qty' => 3],
        ]]);

        $result = Mapper::map($data, ['Rows[]' => [
            'sku' => '@data/items/*/sku',
            'qty' => '@data/items/*/qty',
        ]]);

        $this->assertSame(['Rows' => [
            ['sku' => 'A', 'qty' => 1],
            ['sku' => null, 'qty' => 2],
            ['sku' => 'C', 'qty' => 3],
        ]], $result);
    }

    public function testCollectionsSkipStaticValues(): void
    {
        $data = StringObjects::instance(['items' => [['id' => 1], ['id' => 2]]]);

        $result = Mapper::map($data, ['Rows[]' => ['id' => '@data/items/*/id', 'type' => 'item']]);

        $this->assertSame(['Rows' => [['id' => 1], ['id' => 2]]], $result);
    }

    public function testCollectionsCanBeNested(): void
    {
        $result = Mapper::map(StringObjects::instance(self::order()), [
            'order' => [
                'id' => '@data/transaction_id',
                'lines[]' => ['sku' => '@data/purchase_details/items/*/sku'],
            ],
        ]);

        $this->assertSame([
            'order' => [
                'id' => 'tx_987654321',
                'lines' => [['sku' => 'ITEM-01'], ['sku' => 'ITEM-02']],
            ],
        ], $result);
    }

    public function testOnlyATrailingSuffixMarksACollection(): void
    {
        $data = StringObjects::instance(['items' => [['id' => 1], ['id' => 2]]]);

        $result = Mapper::map($data, [
            'a[]b' => ['id' => '@data/items/*/id'],
            'c[]' => ['id' => '@data/items/*/id'],
        ]);

        $this->assertSame([
            'a[]b' => ['id' => [1, 2]],
            'c' => [['id' => 1], ['id' => 2]],
        ], $result);
    }

    public function testCollectionSuffixOnAScalarValueOnlyRenamesTheKey(): void
    {
        $data = StringObjects::instance(['items' => [['id' => 1], ['id' => 2]]]);

        $result = Mapper::map($data, ['ids[]' => '@data/items/*/id', '[]' => 'x']);

        $this->assertSame(['ids' => [1, 2], '' => 'x'], $result);
    }

    public function testCallbackReceivesEveryKeyAndValue(): void
    {
        $data = StringObjects::instance(self::order());
        $calls = [];

        $result = Mapper::map($data, [
            'PK' => '@data/transaction_id',
            'Items[]' => ['sku' => '@data/purchase_details/items/*/sku'],
        ], function ($key, $value) use (&$calls) {
            $calls[] = [$key, $value];

            return $key === 'PK' ? 'ORDER#' . $value : $value;
        });

        $this->assertSame([
            'PK' => 'ORDER#tx_987654321',
            'Items' => [['sku' => 'ITEM-01'], ['sku' => 'ITEM-02']],
        ], $result);

        $this->assertSame([
            ['PK', 'tx_987654321'],
            ['sku', ['ITEM-01', 'ITEM-02']],
            ['Items', [['sku' => 'ITEM-01'], ['sku' => 'ITEM-02']]],
        ], $calls);
    }

    public function testCallbackResultIsUsedForCollectionColumns(): void
    {
        $data = StringObjects::instance(self::order());

        $result = Mapper::map($data, [
            'Items[]' => ['qty' => '@data/purchase_details/items/*/qty'],
        ], function (string $key, $value) {
            return $key === 'qty' ? array_map('strval', $value) : $value;
        });

        $this->assertSame(['Items' => [['qty' => '2'], ['qty' => '1']]], $result);
    }

    public function testAcceptsCallableStrings(): void
    {
        $data = StringObjects::instance(['name' => 'john']);

        $result = Mapper::map($data, ['name' => '@data/name'], [self::class, 'upper']);

        $this->assertSame(['name' => 'JOHN'], $result);
    }

    /**
     * @param int|string $key
     * @param mixed      $value
     *
     * @return mixed
     */
    public static function upper($key, $value)
    {
        return is_string($value) ? strtoupper($value) : $value;
    }

    public function testWorksWithTheLegacyBehavior(): void
    {
        $data = StringObjects::instance(self::order(), ['behavior' => Behavior::LEGACY]);

        $result = Mapper::map($data, [
            'PK' => '@data/transaction_id',
            'missing' => '@data/nope',
            'Items[]' => ['sku' => '@data/purchase_details/items/*/sku'],
        ]);

        $this->assertSame([
            'PK' => 'tx_987654321',
            'missing' => null,
            'Items' => [['sku' => 'ITEM-01'], ['sku' => 'ITEM-02']],
        ], $result);
    }

    public function testMergeColumnsBuildsRows(): void
    {
        $this->assertSame(
            [['sku' => 'A', 'qty' => 1], ['sku' => 'B', 'qty' => 2]],
            Mapper::mergeColumns(['sku' => ['A', 'B'], 'qty' => [1, 2]])
        );
    }

    public function testMergeColumnsFillsShortColumnsWithNull(): void
    {
        $this->assertSame(
            [['a' => 1, 'b' => 'x'], ['a' => 2, 'b' => null], ['a' => 3, 'b' => null]],
            Mapper::mergeColumns(['a' => [1, 2, 3], 'b' => ['x']])
        );
    }

    public function testMergeColumnsIgnoresColumnKeysAndSkipsInvalidColumns(): void
    {
        $this->assertSame(
            [['a' => 1], ['a' => 2]],
            Mapper::mergeColumns(['a' => [5 => 1, 'k' => 2], 'b' => [], 'c' => 'scalar', 'd' => null])
        );
    }

    public function testMergeColumnsOfNothingIsEmpty(): void
    {
        $this->assertSame([], Mapper::mergeColumns([]));
    }
}
