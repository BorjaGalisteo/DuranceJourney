<?php
declare(strict_types=1);
namespace App\Domain;


class DuranceException extends \Exception
{
    public static function maxNumberOfBagsCreated(): self
    {
        $message = "Is not possible to carry more bags";
        return new self($message);
    }
}