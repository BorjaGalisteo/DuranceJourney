<?php
declare(strict_types=1);


class Category
{
    private string $name;

    /**
     * Category constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

}