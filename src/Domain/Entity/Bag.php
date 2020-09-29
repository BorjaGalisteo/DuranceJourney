<?php
declare(strict_types=1);

namespace App\Domain\Entity;

class Bag
{
    private int $max_items;
    private array $items;
    private ?string $category;

    /**
     * Bag constructor.
     * @param int $max_items
     */
    public function __construct(int $max_items)
    {
        $this->max_items = $max_items;
        $this->items = [];
    }

    /**
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @param string|null $category
     */
    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string|null
     */
    public function category(): ?string
    {
        return $this->category;
    }

    /**
     * @param array $items
     */
    public function addItem(array $items): void
    {
        $this->items[] = $items;
    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        if (count($this->items) >= $this->max_items) {
            return true;
        }
        return false;
    }

    public function emptyBag(): void
    {
        $this->items = [];
    }

}