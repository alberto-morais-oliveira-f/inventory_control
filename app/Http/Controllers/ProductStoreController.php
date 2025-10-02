<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Services\Interfaces\ProductServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class ProductStoreController extends Controller
{
    public function __construct(private readonly ProductServiceInterface $productService) {}

    /**
     * Display a listing of the resource.
     */
    public function __invoke(ProductStoreRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->productService->register($data);

            return response()->json(['message' => 'Produto cadastrado com sucesso!']);
        } catch (Exception|Throwable $exception) {
            return response()->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
