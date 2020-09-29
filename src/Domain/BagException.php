<?php
declare(strict_types=1);

namespace App\Domain;

use Exception;

class BagException extends Exception
{
    public static function bagWasFull(): self
    {
        $message = "Is not possible to save more items in the bag";
        return new self($message);
    }
}