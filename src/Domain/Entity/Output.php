<?php
declare(strict_types=1);
namespace App\Domain\Entity;


class Output
{
    private array $output;
    public const MESSAGE_FIELD = 'message';
    public const DATA_FIELD = 'data';

    /**
     * Output constructor.
     * @param array $output
     */
    public function __construct(array $output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->output[self::MESSAGE_FIELD];
    }
    /**
     * @return string
     */
    public function data(): string
    {
        return $this->output[self::DATA_FIELD];
    }

}