<?php


namespace App\Traits;

use App\Helpers\HyperPay;
use App\Helpers\HyperPayWeb;
use App\Models\DiscountsCode;
use App\Models\HyperpayResults;
use App\Models\MoneyLogs;
use App\Models\Packges;
use App\Models\Userpackges;
use App\Models\UsersCreditCards;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait CouponTrait
{

    public function check_coupon($coupon , $price)
    {


        $used_coupons = HyperpayResults::where('coupon', $coupon->code)->where('is_success', 1)->count();
        if ($used_coupons >= $coupon->quantity) {
            $data['status'] = false;
            $data['message'] = "The coupon has expired";
            return $data;
        }


        if ($coupon->type == 1) {
            $discount = ($coupon->percentage / 100) * $price;
        } else {
            $discount = $coupon->amount;
            if ($discount < 0) {
                $discount = 0;
            }
        }
        $data['status'] = true;
        $data['discount'] = $discount;
        $data['amount_after_discount'] = $price - $discount;

        return $data;
    }

}
