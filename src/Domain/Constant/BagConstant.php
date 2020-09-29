<?php
declare(strict_types=1);
namespace App\Domain\Constant;


class BagConstant
{
    public const MAX_ITEMS_REGULAR_BAG = 4;
    public const MAX_ITEMS_BACKPACK_BAG = 8;
    public const AVAILABLE_CATEGORIES = ["clothes", "weapons", "metals", "herbs", null];
}