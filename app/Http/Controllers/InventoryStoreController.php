<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductException;
use App\Http\Requests\InventoryStoreRequest;
use App\Services\Interfaces\InventoryServiceInterface;
use Exception;
use Throwable;

class InventoryStoreController extends Controller
{
    public function __construct(private readonly InventoryServiceInterface $inventoryService)
    {
    }

    public function __invoke(InventoryStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $this->inventoryService->register($data);

            return response()->json(['message' => 'Inventory registrado com sucesso.']);
        } catch (ProductException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        } catch (Exception|Throwable $exception) {
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }
}
