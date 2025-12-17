<?php

namespace App\Http\Controllers;

use App\Helpers\HyperPay;
use App\Helpers\HyperPayWeb;
use App\Models\Balance;
use App\Models\DiscountsCode;
use App\Models\HyperpayResults;
use App\Models\MoneyLogs;
use App\Models\Packges;
use App\Models\Userpackges;
use App\Traits\CouponTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Appinfos;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    use CouponTrait;

    public function index($id, Request $request)
    {

        if ($request->has('tuid') && $request->has('tpayment_type')) {
            $type = strtoupper($request->tpayment_type) == 'VISA' ? 'VISA MASTER' : strtoupper($request->tpayment_type);
            $price = $request->price;
            $url = url('/payment-callback?checkoutId=' . $id . '&tuid=' . $request->tuid . '&tpayment_type=' . $request->tpayment_type);
//            if (env('APP_ENV') == 'local') {
//            } else {
//                $url = 'https://admin.rootexams.com/payment-callback?checkoutId=' . $id . '&tuid=' . $request->tuid . '&tpayment_type=' . $request->tpayment_type;
//            }
            return view('layouts.checkout-form', compact('url', 'type', 'price', 'id'));

        } else {
            return 'a';
        }
        return redirect()->to(url('/payment-status/error'));
    }

    public function payment_callback(Request $request)
    {
        if ($request->has('tuid')) {
            $response = null;
            if (isset($request->checkoutId) && isset($request->id)
                && $request->id === $request->checkoutId && isset($request->tpayment_type)) {
                $response = HyperPayWeb::validateCheckout($request->checkoutId, $request->tpayment_type, true);
            }

            if (@$response[0]) {

//        dd($response  , preg_match(HyperPay::SUCCESS_CODE_PATTERN, $response['result']['code']));
                $price = $response[1];
                $response = $response[3];
                if (preg_match(HyperPay::SUCCESS_CODE_PATTERN, $response['result']['code'])
                    ||
                    preg_match(HyperPay::SUCCESS_MANUAL_REVIEW_CODE_PATTERN, $response['result']['code'])
                ) {
                    $money_log = MoneyLogs::query()->where('payment_id', $request->checkoutId)->first();
                    HyperPayWeb::register_credit_card($money_log->user_id, $response);
                    $new_hyperpay = new HyperpayResults();
                    $type = 'package';
                    $new_hyperpay->type = $type;
                    $new_hyperpay->user_id = $money_log->user_id;
                    $new_hyperpay->item_id = $money_log->item_id;
                    $new_hyperpay->payment_id = @$response['id'] ?: "";
                    $new_hyperpay->payment_brand = @$response['paymentBrand'] ?: "";
                    $new_hyperpay->transaction_id = @$response['merchantTransactionId'] ?: "";
                    $new_hyperpay->amount = @$response['amount'] ?: "";
                    $new_hyperpay->result = @json_encode($response['result']) ?: "";
                    $new_hyperpay->result_details = @json_encode($response['resultDetails']) ?: "";
                    $new_hyperpay->card = @json_encode($response['card']) ?: "";
                    $new_hyperpay->customer = @json_encode($response['customer']) ?: "";
                    $new_hyperpay->custom_parameters = @json_encode($response['customParameters']) ?: "";
                    $new_hyperpay->is_success = 1;
                    $new_hyperpay->save();

                    $money_log->status = 1;
                    $money_log->save();
                    $package = Packges::find($money_log->item_id);

                    $this->saveSubscription($money_log, $package, $new_hyperpay);
                    return redirect()->away(env('FRONT_END_URL') . 'user/subscriptions');

                } elseif (preg_match(HyperPay::PENDING_HALF_PATTERN, $response['result']['code'])) {
                    return redirect()->to(url('/payment-status/pending'));
                } else {
                    Log::alert('error payment callback:' . $response[2]);
                    return redirect()->to(url('/payment-status/error'));
                }


            } else {
                Log::alert('error in success:' . json_encode($response));
                return redirect()->to(url('/payment-status/error'));
            }
        }
        Log::alert('error after success no tuid');

        return redirect()->to(url('/payment-status/error'));
    }


    public function saveSubscription($money_log, $package, $new_hyperpay)
    {
        $subscribe = Userpackges::query()->where([
            'user_id' => $money_log->user_id,
            'package_id' => $money_log->item_id,
        ])->firstOrNew();

        $subscribe->user_id = $money_log->user_id;
        $subscribe->package_id = $money_log->item_id;


        $subscribe->pay_id = $new_hyperpay->id;


        $discount = 0;
        if ($money_log->coupon != null) {
            $getCoupon = DiscountsCode::query()->where('code', $money_log->coupon)
                ->first();
            $res = $this->check_coupon($getCoupon, $package->price);
            if ($res['status']) {
                $discount = $res['discount'];
            }

        }
        $subscribe->price = $new_hyperpay->amount;
        $subscribe->price_before_discount = $new_hyperpay->amount + $discount;
        $subscribe->discount = $discount;
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
        $new_hyperpay->item_id=$subscribe->id;
        $new_hyperpay->save();
    }

    public function successPayment(Request $request)
    {
        return view('layouts.success_payment');
    }

    public function errorPayment(Request $request)
    {
        return view('layouts.error_payment');
    }

    public function pendingPayment(Request $request)
    {
        Log::alert('pending payment ' . json_encode($request->all()));
        return view('layouts.pending-payment');
    }

}

