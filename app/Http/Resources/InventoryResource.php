<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'name' => $this->name,
            'total_quantity' => (int) $this->total_quantity,
            'total_cost' => (float) $this->total_cost,
            'total_sale' => (float) $this->total_sale,
            'projected_profit' => (float) $this->projected_profit,
        ];
    }
}
