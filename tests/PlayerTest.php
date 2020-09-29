<?php
declare(strict_types=1);

use App\Application\Service\FindItems;
use App\Domain\Constant\BagConstant;
use App\Domain\DTO\Durance;
use App\Domain\Entity\Bag;
use App\Infrastructure\ItemsInMemoryRepository;
use App\Application\Command\FindItemsCommand;
use \PHPUnit\Framework\TestCase;
use Mockery as m;

class PlayerTest extends TestCase
{
    /**
     * @param array $params
     * @param array $expected
     * @dataProvider dataProvider
     * @throws \App\Domain\DuranceException
     */
    public function testPlayer(array $params, array $expected)
    {
        $itemsRepository = m::mock(ItemsInMemoryRepository::class);
        $itemsRepository->shouldReceive('findNewItems')
            ->andReturnUsing(function (int $from, int $quantity) use ($params, $expected) {
                $this->assertEquals($expected['from'], $from);
                $this->assertEquals($expected['quantity'], $quantity);

                return $params['items_found'];
            });

        $durance = new Durance([new Bag(BagConstant::MAX_ITEMS_BACKPACK_BAG)], new FindItems($itemsRepository));
        $items   = $durance->itemSearcher()->handle(new FindItemsCommand($params['from'], $params['quantity']));
        $durance->saveItems($items);

        $total_bags_preorder = $durance->totalBags();

        $this->assertEquals($expected['total_bags'], $durance->totalBags());
        $this->assertEquals($expected['space_for_more_bags'], $durance->hasSpaceForMoreBags());

        $durance->orderBags();
        $this->assertLessThan(6, $durance->totalBags());

        $this->assertEquals($total_bags_preorder, $durance->totalBags());
    }

    public function dataProvider()
    {
        return [
            'case 1 item'      => [
                'params'   => [
                    'from'        => 0,
                    'quantity'    => 4,
                    'items_found' => [['test_item' => 'test_category']],
                ],
                'expected' => [
                    'total_bags'          => 1,
                    'space_for_more_bags' => true,
                    'from'                => 0,
                    'quantity'            => 4,
                ],
            ],
            'case >8 item'     => [
                'params'   => [
                    'from'        => 0,
                    'quantity'    => 9,
                    'items_found' => [
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                        ['test_item6' => 'test_category6'],
                        ['test_item7' => 'test_category7'],
                        ['test_item8' => 'test_category8'],
                    ],
                ],
                'expected' => [
                    'total_bags'          => 2,
                    'space_for_more_bags' => true,
                    'from'                => 0,
                    'quantity'            => 9,
                ],
            ],
            'case 0 item'      => [
                'params'   => [
                    'from'        => 0,
                    'quantity'    => 0,
                    'items_found' => [],
                ],
                'expected' => [
                    'total_bags'          => 1,
                    'space_for_more_bags' => true,
                    'from'                => 0,
                    'quantity'            => 0,
                ],
            ],
            'case limit space' => [
                'params'   => [
                    'from'        => 0,
                    'quantity'    => 9,
                    'items_found' => [
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                        ['test_item6' => 'test_category6'],
                        ['test_item7' => 'test_category7'],
                        ['test_item8' => 'test_category8'],
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                        ['test_item6' => 'test_category6'],
                        ['test_item7' => 'test_category7'],
                        ['test_item8' => 'test_category8'],
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                    ],
                ],
                'expected' => [
                    'total_bags'          => 5,
                    'space_for_more_bags' => false,
                    'from'                => 0,
                    'quantity'            => 9,
                ],
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $expected
     * @dataProvider dataProviderException
     * @throws \App\Domain\DuranceException
     */
    public function testPlayerException(array $params, array $expected)
    {
        $this->expectException(\App\Domain\DuranceException::class);

        $itemsRepository = m::mock(ItemsInMemoryRepository::class);

        $itemsRepository->shouldReceive('findNewItems')
            ->andReturnUsing(function (int $from, int $quantity) use ($params, $expected) {
                $this->assertEquals($expected['from'], $from);
                $this->assertEquals($expected['quantity'], $quantity);

                return $params['items_found'];
            });

        $durance = new Durance([new Bag(BagConstant::MAX_ITEMS_BACKPACK_BAG)], new FindItems($itemsRepository));
        $items   = $durance->itemSearcher()->handle(new FindItemsCommand($params['from'], $params['quantity']));
        $durance->saveItems($items);

    }

    public function dataProviderException()
    {
        return [
            'case more items than space' => [
                'params'   => [
                    'from'        => 0,
                    'quantity'    => 25,
                    'items_found' => [
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                        ['test_item6' => 'test_category6'],
                        ['test_item7' => 'test_category7'],
                        ['test_item8' => 'test_category8'],
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                        ['test_item6' => 'test_category6'],
                        ['test_item7' => 'test_category7'],
                        ['test_item8' => 'test_category8'],
                        ['test_item0' => 'test_category0'],
                        ['test_item1' => 'test_category1'],
                        ['test_item2' => 'test_category2'],
                        ['test_item3' => 'test_category3'],
                        ['test_item4' => 'test_category4'],
                        ['test_item5' => 'test_category5'],
                        ['test_item5' => 'test_category5'],
                    ],
                ],
                'expected' => [
                    'from'                => 0,
                    'quantity'            => 25,
                ],
            ],
        ];
    }

}