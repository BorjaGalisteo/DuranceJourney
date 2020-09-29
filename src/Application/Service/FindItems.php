<?php
declare(strict_types=1);
namespace App\Application\Service;

use App\Application\Command\FindItemsCommand;
use App\Domain\Contract\ItemsRepositoryInterface;

class FindItems
{
    private ItemsRepositoryInterface $itemsRepository;

    /**
     * FindItems constructor.
     * @param ItemsRepositoryInterface $itemsRepository
     */
    public function __construct(ItemsRepositoryInterface $itemsRepository)
    {
        $this->itemsRepository = $itemsRepository;
    }

    public function handle(FindItemsCommand $command):array
    {
        return $this->itemsRepository->findNewItems($command->from(),$command->quantity());
    }


}