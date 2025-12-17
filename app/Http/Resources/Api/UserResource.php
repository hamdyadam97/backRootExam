<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name_short' => strtoupper(mb_substr($this['first_name'], 0, 1, 'utf-8') . '.' . mb_substr($this['last_name'], 0, 1, 'utf-8')) ,
            'first_name' => $this['first_name'],
            'last_name' => $this['last_name'],
            'email' => $this['email'],

            'dial_code' => $this['dial_code'],
            'mobile_country_code' => $this['mobile_country_code'],
            'mobile_number' => $this['mobile_number'],
            'mobile' => $this['mobile'],

            'image' => !empty($this->thumb) ? asset('storage/user_image') . '/' . $this->thumb : null,

            'active_subscriptions_count' =>  $this['activeSubscriptions']->count(),
            'categories' => CategoryResource::collection($this->categories()),
            'sub_categories' => SubCategoryResource::collection($this->subcategories()),
            'sub_sub_categories' => SubSubCategoryResource::collection($this->subSubcategories()),
            'salutation' => $this['salutation'],
            'specialization' => $this['specialization'],
            'governorate' =>$this['governorate'],
        ];
    }
}
