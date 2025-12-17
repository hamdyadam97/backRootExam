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

trait HayperPayTrait
{
    use CouponTrait;

    public function checkout(Request $request, $package, $price, $coupon = null)
    {
        if ($request->platform == 'web') {
            return $this->checkout_web($request);
        }
        $user = $request->user();
//        $user_id = $user->id;
//        $package_id = $request->package_id;
//        $price = $request->price;


//        if (floatval($request->price) != floatval($package->price)) {
//            return $this->send_error('Price not match', ['PackageNotFound' => ['price not match']], 400);
//        }
//        $coupon = $request->coupon;
        $discount = 0;
        $price_after_discount = $package->price;

        if ($request->coupon) {
            $getCoupon = DiscountsCode::query()->where('code', $coupon)
                ->where('status', 1)
                ->where('from_date', '<=', Carbon::now())
                ->where('to_date', '>=', Carbon::now())
                ->select('id', 'code', 'type', 'percentage', 'amount', 'quantity')
                ->first();

            if (!$getCoupon) {
                return $this->send_error('Invalid coupon', ['CouponNotFound' => ['No coupon found']], 403);
            }
            if ($package->type_id == 1) {
                $discount = ($package->price / 100) * ($getCoupon->percentage);
                $price_after_discount = $package->price - $discount;
                if ($getCoupon->percentage == 100) {
                    $price_after_discount = 0;
                    $discount = $package->price;
                }
            } else {
                $discount = $getCoupon->amount;
                $price_after_discount = $package->price - $getCoupon->amount;
                if ($price_after_discount < 0) {
                    $price_after_discount = 0;
                    $discount = $package->price;
                }
            }
        }


        $checkout_id = HyperPay::getChekoutId($package, $request, $discount);
        if ($checkout_id) {
//            Log::alert('mobile checkoutId:'.$checkout_id);
            return $this->send_response('Success', ['checkout_id' => $checkout_id], 200);

        } else {
            return $this->send_error('Error get checkout id', null, 400);
        }

    }

    public function checkout_web($request)
    {

        $user = $request->user();
        $user_id = $user->id;
        if (empty($user_id)) {
            return $this->api_response(false, 'Missing parameter user_id', [], 422);
        }

        $package_id = $request->package_id;
        $package = Packges::find($package_id);
        if (empty($package)) {
            return $this->api_response(false, 'No package found', [], 422);
        }

        $payment_type = $request->payment_type;
        $price = $package->price;


        $coupon = $request->coupon;
        $discount = 0;
        if ($request->coupon) {
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
//                $price_after_discount = $res['amount_after_discount'];
                $discount = $res['discount'];

            } else {
                return $this->api_response(false, $res['message'], [], 422);
            }
        }


        $rules = [
            'package_id' => 'required',
//            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'payment_type' => 'required|in:visa,mada',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        return $this->initPayment($user, $price, $discount, $package);


    }

    public function initPayment($user, $price, $discount, $package)
    {
        $type = \request()->payment_type;
        $billing = [
            'billing.street1' => 'zeid bin hareth',
//            'billing.street1' => @$user->street,
            'billing.city' => 'swileih',
//            'billing.city' => @$user->state->name,
            'billing.state' => 'Amman',
//            'billing.state' => @$user->state->name,
            'billing.country' => 'JO',
            'billing.postcode' => '111114',
            'customer.email' => $user->email ?: $user->mobile . '@rootexam.com',
            'customer.givenName' => $user->first_name,
            'customer.surname' => $user->last_name ?? $user->first_name,
        ];
//        $tokens = UsersCreditCards::query()->where('user_id', $user->user_id)->pluck('token')->toArray();
        $tokens = [];

        $merchantTransactionId = $user->id . Str::random('4');
        $result = HyperPayWeb::pay(number_format((float)$price, 2, '.', '')
            , $type, $billing, $merchantTransactionId, $tokens, $discount);

        if (isset($result['id'])) {
            $unique_id = "#" . $user->id . $package->id . Str::random(4);
            $price -= $discount;
            $query = '?tpayment_type=' . \request()->payment_type . '&tuid=' . $user->id . $package->id . Str::random(4) . '&price=' . $price;
            $id = $result['id'];

            $payment_log = new MoneyLogs();
            $payment_log->payment_id = $id;
            $payment_log->platform = "web";
            $payment_log->coupon = \request()->coupon;
            $payment_log->item_id = $package->id;
            $payment_log->user_id = $user->id;
            $payment_log->unique_id = $unique_id;
            $payment_log->save();


            return response()->json([
                'status' => true,
                'message' => __('Redirect to payment link'),
                'data' => [
                    'url' => url('/payment/' . $result['id']) . $query
                ],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something was wrong',
                'data' => [],
            ], 500);
        }
    }

    public function package_payment_status(Request $request)
    {
        $id = $request->checkout_id ? $request->checkout_id : $request->id;
//        Log::alert('mobile callback:'.$id);
//        Log::alert('mobile callback:'.$id);
        $money_log = MoneyLogs::query()->where('payment_id', $id)->where('platform', 'mobile')->first();
        $user = User::find($money_log->user_id);
        $package = Packges::find($money_log->item_id);
        if (!$money_log || !$package || !$user) {
            return $this->send_error('Sorry This payment not has package', null, 400);

//            return response()->json(
//                [
//                    'status' => 400 ,
//                    'message' => "عفوا هذه العملية غير مربوطة بطلب" ,
//                ]
//            );
        }
        if ($money_log->status == 1) {
            return $this->send_response('Successful payment', null, 200);
        }


        $response = HyperPay::validateCheckoutRegistration($user, $money_log, $id);
        $values = $response['values'];
//        Log::alert('mobile res');
//        Log::alert(json_encode($response));
        if ($response['code'] === HyperPay::SUCCESS_RESPOSNE) {
            $new_hyperpay = new HyperpayResults();
            $type = 'package';
            $new_hyperpay->type = $type;
            $new_hyperpay->coupon = $money_log->coupon;
            $new_hyperpay->user_id = $money_log->user_id;
            $new_hyperpay->item_id = $money_log->item_id;
            $new_hyperpay->payment_id = @$values->id ?: "";
            $new_hyperpay->payment_brand = @$values->paymentBrand ?: "";
            $new_hyperpay->transaction_id = @$values->merchantTransactionId ?: "";
            $new_hyperpay->amount = @$values->amount ?: "";
            $new_hyperpay->result = @json_encode($values->result) ?: "";
            $new_hyperpay->result_details = @json_encode($values->resultDetails) ?: "";
            $new_hyperpay->card = @json_encode($values->card) ?: "";
            $new_hyperpay->customer = @json_encode($values->customer) ?: "";
//            $new_hyperpay->custom_parameters = @json_encode($response['customParameters']) ?: "";
            $new_hyperpay->custom_parameters = @json_encode($response);
            $new_hyperpay->is_success = 1;
            $new_hyperpay->save();

            $money_log->status = 1;
            $money_log->save();


            $package = Packges::find($money_log->item_id);

            $subscribe = new Userpackges;
            $subscribe->user_id = $money_log->user_id;
            $subscribe->package_id = $money_log->item_id;
//                    start date and end date get from period
            $subscribe->start_date = Carbon::now();
            $subscribe->end_date = Carbon::now()->addDays(intval($package->period));
            $discount = 0;
            if ($money_log->coupon != null) {
                $getCoupon = DiscountsCode::where('code', $money_log->coupon)
                    ->first();
                if ($package->type_id == 1) {
                    $discount = ($package->price / 100) * ($getCoupon->percentage);
                } else {
                    $discount = $package->price - $getCoupon->amount;
                    if ($discount < 0) {
                        $discount = $package->price;
                    }
                }
            }
            $subscribe->price = $new_hyperpay->amount;
            $subscribe->price_before_discount = floatval($new_hyperpay->amount) + $discount;
            $subscribe->discount = $discount;
            $subscribe->pay_id = @$new_hyperpay->id;
            $subscribe->save();
            $new_hyperpay->item_id=$subscribe->id;
            $new_hyperpay->save();


            return $this->send_response('Successful payment', ['data' => [$values], 'res' => @$response, 'package_id' => [$money_log->item_id]], 200);

        } else if ($response['code'] === HyperPay::PENDING_RESPONSE) {
            return $this->send_error('Waiting for payment', null, 400);

        } else {
            return $this->send_error(@$values->result->description . " ( " . @$values->result->code . " ) ", ['data' => [$values]], 400);


        }
    }

}
