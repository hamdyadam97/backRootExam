<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\PackageResource;
use App\Http\Resources\Api\SubCategoryResource;
use App\Http\Resources\Api\UserPackageResource;
use App\Models\Category;
use App\Models\DiscountsCode;
use App\Models\MoneyLogs;
use App\Models\SubCategory;
use App\Traits\CouponTrait;
use App\Traits\HayperPayTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Packges;
use App\Models\Userpackges;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PackageController extends BaseController
{
    use  HayperPayTrait, CouponTrait;

    // get all packages
    public function data()
    {

        $categories = Category::query()
            ->where('status', 1)
            ->get();
        $sub_categories = SubCategory::query()
            ->where('status', 1)
            ->get();

        $data['categories'] = CategoryResource::toPackage($categories);
        $data['sub_categories'] = SubCategoryResource::toPackage($sub_categories);
        return $this->api_response(true, 'Packages data', $data);
    }

    public function index()
    {
        ini_set('display_errors', 1);
        $packages = Packges::query()
            ->with([
                    'category' => function ($q) {
                        $q->withCount('questions');
                    },
                    'subCategories' => function ($q) {
                        $q->withCount('questions');
                    }]
            )
            ->where('is_trial', 0)
            ->where('status', 1)
            ->filter()
            ->get();

//        $categories = Category::query()
//            ->where('status', 1)
//            ->withCount(['questions' => function ($q) {
//                $q->where('status', 1);
//            }])
//            ->get();
//        $sub_categories = SubCategory::query()->where('status', 1)
//            ->withCount(['questions' => function ($q) {
//                $q->where('status', 1);
//            }])
//            ->get();
        $data['packages'] = PackageResource::collection($packages);
//        $data['categories'] = CategoryResource::collection($categories);
//        $data['sub_categories'] = SubCategoryResource::collection($sub_categories);
//        dd($data);
        $data['default_image'] = asset('default.png');
//dd($data);
        return $this->api_response(true, 'Packages data', $data);
    }

    public function getPackage($id)
    {
        $package = Packges::query()->where('status', 1)->find($id);
        if (!$package) {
            return $this->api_response(false, 'Package not found', [], 404);
        }
        $data['package'] = new PackageResource($package);
        return $this->api_response(true, 'Packages data', $data);
    }

    // subscribe api method
    public function subscribe(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->send_error('No user found', ['UserNotFound' => ['No user found']], 400);
        }

        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id,status,1'
        ]);

        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        $user_id = $user->id;
        $package_id = $request->package_id;
        $package = Packges::query()->find($package_id);
        $price = $package->price;
//        $request['platform'] = "web";
        $payment_type = $request->payment_type;

        $discount = 0;
        $price_after_discount = $price;
        if ($request->coupon) {
            $coupon = $request->coupon;
            $getCoupon = DiscountsCode::where('code', $coupon)
                ->where('status', 1)
                ->where('from_date', '<=', Carbon::now())
                ->where('to_date', '>=', Carbon::now())
                ->select('id', 'code', 'type', 'percentage', 'amount', 'quantity')
                ->first();
            if (!$getCoupon) {
                return $this->api_response(false, 'No coupon found', [], 422);
            }

            $res = $this->check_coupon($getCoupon, $price);
            if ($res['status']) {
                $price_after_discount = $res['amount_after_discount'];
                $discount = $res['discount'];

            } else {
                return $this->api_response(false, $res['message'], [], 422);
            }
        }

        if ($price_after_discount == 0) {
            $this->saveTransaction($request);
            return $this->api_response(true, 'Package subscribed');
        }


        return $this->checkout_web($request);
        //dd($this->checkout($request, $package , $package->price , $request->coupon));
//        if (!$package) {
//            return $this->send_error('No package found', ['PackageNotFound' => ['No package found']], 400);
//        }
//
//        $this->saveTransaction($request);
//        return $this->api_response(true, 'Package subscribed');

    }

    public function userSubscription(Request $request)
    {
        $user = $request->user();
        $data['subscriptions'] = UserPackageResource::collection($user->subscriptions()->orderByDesc('updated_at')->get());
        return $this->api_response(true, 'Packages data', $data);
    }


    public function saveTransaction(Request $request)
    {
        $package = Packges::query()->find($request->package_id);
        $user = $request->user();
        $money_log = MoneyLogs::query()->create([
            'platform' => 'web',
            'item_id' => $package->id,
            'unique_id' => Str::uuid()->toString(),
            'payment_id' => Str::uuid()->toString(),
            'status' => 1,
            'user_id' => $user->id,
            'coupon' => $request->coupon,
        ]);

//        $subscribe = Userpackges::query()->create([
//            'user_id' => $money_log->user_id,
//            'package_id' => $money_log->item_id,
//            'start_date' => now(),
//            'end_date' => now()->addDays($package->period),
//        ]);
//
//        return true;


        $subscribe = Userpackges::query()->where([
            'user_id' => $money_log->user_id,
            'package_id' => $money_log->item_id,
        ])->firstOrNew();

        $subscribe->user_id = $money_log->user_id;
        $subscribe->package_id = $money_log->item_id;


        $discount = 0;
        if ($money_log->coupon != null) {
            $getCoupon = DiscountsCode::query()->where('code', $money_log->coupon)
                ->first();
            $res = $this->check_coupon($getCoupon, $package->price);
            if ($res['status']) {
                $discount = $res['discount'];
            }

        }
        $subscribe->price = 0;
        $subscribe->price_before_discount = 0;
        $subscribe->discount = 0;
        if (isset($subscribe->id)) {
            $subscribe->end_date = Carbon::parse($subscribe->end_date)->addDays(intval($package->period));
            if (!$subscribe->subscription_status){
                $subscribe->subscription_status = 1;
            }
        } else {
            $subscribe->start_date = Carbon::now();
            $subscribe->end_date = Carbon::now()->addDays(intval($package->period));
        }
        $subscribe->save();

        return true;

    }


}
