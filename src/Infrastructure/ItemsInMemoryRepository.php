<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Contract\ItemsRepositoryInterface;

class ItemsInMemoryRepository implements ItemsRepositoryInterface
{
    private const DEFAULT_QUANTITY = 0;
    private array $items;

    /**
     * ItemsInMemoryRepository constructor.
     */
    public function __construct()
    {
        $this->items = [];
    }


    /**
     * @param int $from
     * @param int $quantity
     * @return array
     */
    public function findNewItems(int $from, int $quantity): array
    {
        $this->normalizeItems();

        $items = [];
        $j     = self::DEFAULT_QUANTITY;
        $index = $from;
        for ($i = $from; $j < $quantity; $j++) {
            $items[] = $this->items[$index];
            $index++;
        }
        return $items;
    }

    private function normalizeItems(): void
    {
        $items_raw = json_decode('{
          "Gold": "metals",
          "Leather": "clothes",
          "Linen": "clothes",
          "Mace":"weapons",
          "Silk": "clothes",
          "Wool": "clothes",
          "Cooper": "metals",
          "Cherry Blossom":"herbs",
          "Marigold":"herbs",
          "Iron": "metals",
          "Silver": "metals",
          "Axe": "weapons",
          "Dagger":"weapons",
          "Sword":"weapons",
          "Rose":"herbs",
          "Seaweed":"herbs"
        }');
        foreach ($items_raw as $item => $category) {
            $this->items[] = [$item => $category];
        }
        return;
    }
}