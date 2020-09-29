<?php
declare(strict_types=1);
namespace App\Application\Command;


class FindItemsCommand
{
    private int $from;
    private int $quantity;

    /**
     * FindItemsCommand constructor.
     * @param int $from
     * @param int $quantity
     */
    public function __construct(int $from, int $quantity)
    {
        $this->from     = $from;
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function from(): int
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }

}