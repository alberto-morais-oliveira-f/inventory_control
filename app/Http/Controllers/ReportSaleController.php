<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReportRequest;
use App\Http\Resources\SaleResource;
use App\Services\Interfaces\SaleServiceInterface;

class ReportSaleController extends Controller
{
    public function __construct(private readonly SaleServiceInterface $saleService)
    {
    }

    public function __invoke(SalesReportRequest $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'product_sku']);

        $salesPaginator = $this->saleService->getSalesReport($filters);

        $salesData = SaleResource::collection($salesPaginator)->resolve();

        // ResourceCollection com paginação
        return response()->json([
            'data' => $salesData,
            'paginate' => [
                'first' => $salesPaginator->url(1),
                'last' => $salesPaginator->url($salesPaginator->lastPage()),
                'prev' => $salesPaginator->previousPageUrl(),
                'next' => $salesPaginator->nextPageUrl(),
                'current_page' => $salesPaginator->currentPage(),
                'per_page' => $salesPaginator->perPage(),
                'total' => $salesPaginator->total(),
            ],
        ]);
    }
}
