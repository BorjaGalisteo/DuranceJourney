<?php

use App\Application\Service\FindItems;
use App\Domain\Constant\BagConstant;
use App\Domain\DTO\Durance;
use App\Domain\DTO\Menu;
use App\Domain\Entity\Bag;
use \Wujunze\Colors;
use App\Infrastructure\ItemsInMemoryRepository;

require_once realpath("vendor/autoload.php");

$color  = new Colors();
$durance = new Durance([new Bag(BagConstant::MAX_ITEMS_BACKPACK_BAG)], (new FindItems(new ItemsInMemoryRepository())));
$menu    = new Menu($durance);
$output_template = "%s : %s";

$stdin = fopen('php://stdin', 'r');

while (true) {
    echo $menu->renderMenu();

    $input = trim(fgets($stdin));
    if (is_numeric($input)){
        $output = $menu->handle($input);
        echo $color->getColoredString(sprintf($output_template,$output->message(),$output->data()) , "cyan", "black", true);
    }else{
        echo $color->getColoredString($menu::INVALID_OPTION_MESSAGE, "cyan", "black", true);
    }

}


