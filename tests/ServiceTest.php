<?php
declare(strict_types=1);

use App\Application\Service\FindItems;
use App\Infrastructure\ItemsInMemoryRepository;
use App\Application\Command\FindItemsCommand;
use \PHPUnit\Framework\TestCase;
use Mockery as m;

class ServiceTest extends TestCase
{
    /**
     * @param array $params
     * @param array $expected
     * @dataProvider dataProvider
     */
    public function testFindItems(array $params, array $expected)
    {
        $itemsRepository = m::mock(ItemsInMemoryRepository::class);
        $itemsRepository->shouldReceive('findNewItems')
            ->andReturnUsing(function (int $from, int $quantity) use ($expected) {
                $this->assertEquals($expected['from'], $from);
                $this->assertEquals($expected['quantity'], $quantity);
                return [];
            });
        $durance = new FindItems($itemsRepository);
        $durance->handle(new FindItemsCommand($params['from'], $expected['quantity']));
    }

    public function dataProvider()
    {
        return [
            'base' => [
                'params'   => [
                    'from'     => 0,
                    'quantity' => 4,
                ],
                'expected' => [
                    'from'     => 0,
                    'quantity' => 4,
                ],
            ],
        ];
    }
}