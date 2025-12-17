<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'package_name' => $this['getPackage']?->name ??'-',
            'start_date' => $this['start_date'],
            'end_date' => $this['end_date'],
            'status' => $this['subscription_status'],
            'price' => $this['price'],
            'price_before_discount' => $this['price_before_discount'],
            'discount' => $this['discount'],
        ];
    }
}
