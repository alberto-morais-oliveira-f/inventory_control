<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Services\Interfaces\InventoryServiceInterface;

class InventoryListController extends Controller
{
    public function __construct(private readonly InventoryServiceInterface $inventoryService) {}

    public function __invoke()
    {
        $dataInventory = $this->inventoryService->getInventory();

        return InventoryResource::collection($dataInventory);
    }
}
