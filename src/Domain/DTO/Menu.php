<?php
declare(strict_types=1);

namespace App\Domain\DTO;

use App\Application\Command\FindItemsCommand;
use App\Domain\DuranceException;
use App\Domain\Entity\Output;
use Wujunze\Colors;

class Menu
{
    public const OPTION_PICK_ITEMS = 0;
    public const OPTION_GET_BAGS = 1;
    public const OPTION_ORGANIZE_BAGS = 2;
    public const OPTION_EXIT = 3;

    public const INVALID_OPTION_MESSAGE = "Don't be naughty just choose a right option.";

    private Durance $player;

    /**
     * Menu constructor.
     * @param Durance $player
     */
    public function __construct(Durance $player)
    {
        $this->player = $player;
    }


    public function renderMenu(): string
    {
        $color   = new Colors();
        $message = <<<EOD
        You are in the Durance's journey
        What would you like to do?
        [0] Look for some items.
        [1] Look into bags.
        [2] Organize bags.
        [3] Finish Durance's adventure.
        EOD;
        return $color->getColoredString($message, "cyan", "white", true);
    }

    public function handle(int $input): Output
    {
        if ($input == self::OPTION_EXIT) {
            exit();
        }
        if ($input == self::OPTION_PICK_ITEMS) {
            $number_items_found = rand(1, 10);
            $response           = $this->collectItems($number_items_found);
            return new Output([
                Output::MESSAGE_FIELD => $response,
                Output::DATA_FIELD    => (string)$number_items_found,
            ]);
        }
        if ($input == self::OPTION_GET_BAGS) {
            return new Output([
                Output::MESSAGE_FIELD => "That's your bag's content",
                Output::DATA_FIELD    => json_encode($this->player->getItemsByBag()),
            ]);
        }
        if ($input == self::OPTION_ORGANIZE_BAGS) {
            $this->player->orderBags();
            return new Output([
                Output::MESSAGE_FIELD => 'Bags Ordered!',
                Output::DATA_FIELD    => json_encode($this->player->getItemsByBag()),
            ]);
        }
        return new Output([
            Output::MESSAGE_FIELD => self::INVALID_OPTION_MESSAGE,
            Output::DATA_FIELD    => '',
        ]);
    }

    /**
     * @param int $number_items_found
     * @return array
     */
    public function pickupItems(int $number_items_found): array
    {
        $current_items = $this->player->countItems();
        $items         = $this->player->itemSearcher()->handle(new FindItemsCommand($current_items,
            $number_items_found));
        return $items;
    }

    private function collectItems(int $number_items_found): string
    {
        $items = $this->pickupItems($number_items_found);
        try {
            $this->player->saveItems($items);
            return 'Items found and saved in your bag';
        } catch (DuranceException $e) {
            return $e->getMessage();
        }
    }
}