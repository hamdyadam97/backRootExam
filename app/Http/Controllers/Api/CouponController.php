<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountsCode;
use App\Models\HyperpayResults;
use App\Models\Packges;
use App\Models\PaymentType;
use App\Traits\CouponTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends BaseController
{
    use CouponTrait;

    public function index(Request $request)
    {
        $rules = [
            'package_id' => 'required',
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'coupon' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }
        $user = $request->user();
        $user_id = $user->id;
        $package_id = $request->package_id;
        $price = $request->price;
        $coupon = $request->coupon;
        $getCoupon = DiscountsCode::where('code', $coupon)
            ->where('status', 1)
            ->where('from_date', '<=', Carbon::now())
            ->where('to_date', '>=', Carbon::now())
            ->select('id', 'code', 'type', 'percentage', 'amount', 'quantity')
            ->first();
        if (!$getCoupon) {
            return $this->send_error('Invalid coupon', ['CouponNotFound' => ['No coupon found']], 403);
        }
        $used_coupons = HyperpayResults::where('coupon', $getCoupon->code)->where('is_success', 1)->count();
        if ($used_coupons >= $getCoupon->quantity) {
            return $this->send_error('The coupon has expired', ['ExpiredFound' => ['Expired coupon']], 403);
        }
        if (empty($user_id)) {
            return $this->send_error('Missing required parameter', ['MissingUserId' => ['Missing parameter user_id']], 403);
        }
        if (empty($user)) {
            return $this->send_error('No user found', ['UserNotFound' => ['No user found']], 400);
        }
        if (empty($package_id)) {
            return $this->send_error('Missing required parameter', ['MissingPackageId' => ['Missing parameter package_id']], 403);
        }
        if (empty($coupon)) {
            return $this->send_error('Missing required parameter', ['MissingCoupon' => ['Missing parameter coupon']], 403);
        }
        $package = Packges::find($package_id);
        if (empty($package)) {
            return $this->send_error('No package found', ['PackageNotFound' => ['No package found']], 403);
        }
        if (empty($price)) {
            return $this->send_error('Missing required parameter', ['MissingPackageId' => ['Missing parameter price']], 403);
        }
        if (floatval($request->price) != floatval($package->price)) {
            return $this->send_error('Price not match', ['PackageNotFound' => ['price not match']], 403);
        }


        if ($package->type_id == 1) {
            $discount = ($package->price / 100) * ($getCoupon->percentage);
        } else {
            $discount = $package->price - $getCoupon->amount;
            if ($discount < 0) {
                $discount = $package->price;
            }
        }
        $getCoupon->amount_after_discount = $discount;


        return $this->send_response('Coupon available', $getCoupon, 200);


    }

    public function checkCoupon(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'coupon' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->api_response(false, 'Validation Errors', $validator->errors(), 422);
        }
        $user = $request->user();
        $user_id = $user->id;
        $package_id = $request->package_id;
        $package = Packges::query()->find($package_id);

        if (!$package) {
            return $this->api_response(false, 'Package not found', [], 422);
        }

        $price = $package->price;
        $data['price'] = $price;
        $coupon = $request->coupon;
        $getCoupon = DiscountsCode::where('code', $coupon)
            ->where('status', 1)
            ->where('from_date', '<=', Carbon::now())
            ->where('to_date', '>=', Carbon::now())
            ->select('id', 'code', 'type', 'percentage', 'amount', 'quantity')
            ->first();
        if (!$getCoupon) {
            return $this->api_response(false, 'Coupon not found', [], 422);
        }

        $res = $this->check_coupon($getCoupon, $price);
        if ($res['status']) {
            unset($res['status']);
            return $this->api_response(true, 'Coupon available', $res);
        } else {
            return $this->api_response(false, $res['message'], [], 422);
        }

    }
}
