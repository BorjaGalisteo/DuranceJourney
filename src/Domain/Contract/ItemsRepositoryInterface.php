<?php
declare(strict_types=1);
namespace App\Domain\Contract;

interface ItemsRepositoryInterface
{
    public function findNewItems(int $from, int $quantity): array;
}