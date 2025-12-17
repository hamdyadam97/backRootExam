<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Package\PackageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\User;
use App\Models\Userpackges;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user_subscription = Userpackges::where('user_id',$this->id)->latest()->first();
        return [
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'mobile_number'=>$this->mobile,
            'image'=>!empty($this->thumb)?asset('storage/user_image').'/'.$this->thumb:null,
            'device_id'=>$this->device_id,
            'score'=>$this->score,
            'role'=>!empty($this->role_type)?User::$role[$this->role_type]:null,
            'token' =>$this->token,
            'subscription_id'=>!empty($user_subscription->id)?$user_subscription->id:null,
            'package'=>@$user_subscription->getPackage?new PackageResource(@$user_subscription->getPackage):null,
            'user_package'=>@$user_subscription,
            'birth_date' => ($request->birth_date) ? date('Y-m-d', strtotime($request->birth_date)) : NULL,
            'salutation' => $this->salutation,,
            'specialization' => $this->specialization,,
            'governorate' =>$this->governorate,,

        ];
    }
}
