<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleStoreRequest;
use App\Services\Interfaces\SaleServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SaleStoreController extends Controller
{
    public function __construct(private readonly SaleServiceInterface $saleService)
    {
    }

    public function __invoke(SaleStoreRequest $request): JsonResponse
    {
        try {
            $this->saleService->register($request->validated());

            return response()->json(['message' => 'Venda registrada com sucesso!.']);
        } catch (\Exception $exception) {
            dd($exception);
            return response()->json(['message' => 'Error inesperado do servidor'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
