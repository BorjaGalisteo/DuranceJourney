<?php
declare(strict_types=1);

namespace App\Domain\DTO;

use App\Application\Service\FindItems;
use App\Domain\Constant\BagConstant;
use App\Domain\DuranceException;
use App\Domain\Entity\Bag;

class Durance
{
    private const MAX_BAGS = 5;
    /** @var array|Bag[] */
    private array $bags;
    private FindItems $itemSearcher;

    /**
     * Durance constructor.
     * @param array|Bag[] $bags
     * @param FindItems $itemSearcher
     */
    public function __construct(array $bags, FindItems $itemSearcher)
    {
        $this->bags         = $bags;
        $this->itemSearcher = $itemSearcher;
    }

    /**
     * @return array|Bag[]
     */
    public function bags()
    {
        return $this->bags;
    }

    /**
     * @return Bag
     */
    public function bigBag()
    {
        return $this->bags[0];
    }

    /**
     * @return int
     */
    public function countItems(): int
    {
        $quantity = 0;
        /** @var Bag $bag */
        foreach ($this->bags ?? [] as $bag) {
            $quantity += count($bag->items());
        }

        return $quantity;
    }

    /**
     * @return array
     */
    public function getItemsByBag()
    {
        $content = [];
        foreach ($this->bags() ?? [] as $index => $bag) {
            $content[] = $bag->items();
        }

        return $content;
    }

    /**
     * @return int
     */
    public function totalBags(): int
    {
        return count($this->bags());
    }

    /**
     * @param array $items
     * @throws DuranceException
     */
    public function saveItems(array $items)
    {
        foreach ($items as $item) {
            $this->saveItem($item);
        }
        return;
    }

    /**
     * @return FindItems
     */
    public function itemSearcher(): FindItems
    {
        return $this->itemSearcher;
    }

    /**
     * @param array $item
     * @throws DuranceException
     */
    private function saveItem(array $item)
    {
        foreach ($this->bags as $bag) {
            if (!$bag->isFull()) {
                $bag->addItem($item);
                return;
            }
        }
        if ($this->hasSpaceForMoreBags()) {
            $this->addBag();
            $this->saveItem($item);
            return;
        }
        throw DuranceException::maxNumberOfBagsCreated();
    }

    /**
     *
     */
    public function orderBags(): void
    {
        //list all items
        $items = $this->getItems();
        //order it alphabetically
        $ordered_items = $this->orderItems($items);
        //Group items by categories.
        $grouped_items = $this->groupItemsByCategory($ordered_items);
        //Fill the bags with items grouped.
        $this->emptyBags();
        $this->fillBags($grouped_items);
    }

    /**
     *
     */
    private function addBag()
    {
        $this->bags[] = new Bag(BagConstant::MAX_ITEMS_REGULAR_BAG);
    }

    /**
     * @return bool
     */
    public function hasSpaceForMoreBags(): bool
    {
        return $this->totalBags() < self::MAX_BAGS;
    }

    /**
     * @return array
     */
    private function getItems()
    {
        $items = [];
        foreach ($this->bags() ?? [] as $bag) {
            foreach ($bag->items() ?? [] as $item) {
                foreach ($item as $i => $category) {
                    $items[$i] = $category;
                }
            }
        }

        return $items;
    }

    /**
     * @param array $items
     * @return array
     */
    private function orderItems(array $items): array
    {
        ksort($items);

        return $items;
    }

    /**
     * @param array $items
     * @return array
     */
    private function groupItemsByCategory(array $items): array
    {
        $grouped_items = [];
        foreach ($items ?? [] as $item => $category) {
            if (!array_key_exists($category, $grouped_items)) {
                $grouped_items[$category] = [];
            }
            $grouped_items[$category][] = $item;
        }

        return $grouped_items;
    }

    /**
     * @param array $items
     */
    private function fillBags(array $items): void
    {
        $categories = $this->getCategoriesFound($items);
        $this->tagBagsWithCategories($categories);
        foreach ($items as $category => $items_category) {
            foreach ($items_category ?? [] as $item) {
                $item_was_added = false;
                for ($bag_index = 1; $bag_index < $this->totalBags(); $bag_index++) {
                    $bag = $this->bags[$bag_index];
                    if ($this->isRightBagForItem($bag, $category)) {
                        if (!$bag->isFull()) {
                            $bag->addItem([$item => $category]);
                            $item_was_added = true;
                            break;
                        }
                        if (!$this->bigBag()->isFull()) {
                            $this->bigBag()->addItem([$item => $category]);
                            $item_was_added = true;
                        }
                    }
                }
                if (!$item_was_added && !$this->bigBag()->isFull()) {
                    $this->bigBag()->addItem([$item => $category]);
                }
            }
        }

    }

    /**
     * @param array $items
     * @return array
     */
    private function getCategoriesFound(array $items): array
    {
        $categories = [];
        foreach ($items ?? [] as $category => $item) {
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     *
     */
    private function emptyBags()
    {
        /** @var Bag $bag */
        foreach ($this->bags() as $bag) {
            $bag->emptyBag();
        }
    }

    /**
     * @param array $categories
     */
    private function tagBagsWithCategories(array $categories): void
    {
        foreach ($categories ?? [] as $index => $category) {
            //I hate to use +1... the righ way should be checking if the bag is backpack or not.
            $bag_index = $index + 1;
            if (array_key_exists($bag_index, $this->bags())) {
                $this->bags()[$bag_index]->setCategory($category);
            }
        }
    }

    /**
     * @param Bag $bag
     * @param $category
     * @return bool
     */
    private function isRightBagForItem(Bag $bag, $category): bool
    {
        return $bag->category() == $category;
    }
}