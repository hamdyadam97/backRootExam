<?php

namespace App\Helpers;

use App\Http\Resources\OrderNotificationResource;
use App\Huawei\Huawei;
use App\Models\Balance;
use App\Models\ClientBalance;
use App\Models\Dafater;
use App\Models\HyperpayResults;
use App\Models\MoneyLogs;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\OrdersDetails;
use App\Models\User;
use App\Models\UsersCreditCards;
use App\Repositories\API\DonatesRepository;
use App\Repositories\API\UserPackageRepository;
use App\Repositories\Utils\UtilsRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class HyperPay
{

//    const SUCCESS_CODE_PATTERN = '/^(000\.000\.|000\.100\.1|000\.[36])/';
//    const SUCCESS_MANUAL_REVIEW_CODE_PATTERN = "/^(000\.400\.0[^3]|000\.400\.100)/";

    const SUCCESS_CODE_PATTERN = '/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/';
    const SUCCESS_MANUAL_REVIEW_CODE_PATTERN = "/^(000.400.0[^3]|000.400.100)/";
    const PENDING_HALF_PATTERN = '/^(000\.200)/';


    const SUCCESS_RESPOSNE = 1;
    const PENDING_RESPONSE = 2;
    const FAILED_RESPONSE = 0;

    public static function getChekoutId($package, $request,$discount)
    {
        $user=auth()->user();
        $tokens = UsersCreditCards::query()->where('user_id', $user->user_id)->pluck('token')->toArray();



        $email = @$user->email ?$user->email: $user->user_id . "@qodoraty.com";

        $unique_id = "#".$user->id.$package->id.Str::random(4);

        $price=$package->price-$discount;

        $result = self::createCheckout($price, $request->payment_type ,
            $email, "/api/package-payment" , $unique_id , $tokens);
        $values = $result['values'];
        if ($result['response'] === false || !isset($values->id)){
            return  0;
        }

        $payment_log = new MoneyLogs();
        $payment_log->payment_id = @$values->id;
        $payment_log->platform = "mobile";
        $payment_log->coupon = $request->coupon;
        $payment_log->item_id = $package->id;
        $payment_log->user_id = $user->id;
        $payment_log->unique_id = $unique_id;
        $payment_log->save();

        return @$values->id;
    }

    public static function createCheckout($amount, $payment_type, $email, $notificationUrl, $unique_id, $tokens)
    {
        if (env('APP_ENV')=='local'){
            $url=url('/');
        }else{
            $url='https://admin.qodoraty.com';
        }
        $entityId =  (strtolower($payment_type) === HyperPayWeb::VISA) ? env('HYPERPAY_ENTITY_ID') : env('HYPERPAY_MADA_ENTITY_ID');
        $data = "entityId=" . $entityId .
            "&amount=" . $amount .
            "&currency=JOD" .
            "&paymentType=" . 'DB' .
            "&customer.email=" . $email .
            "&notificationUrl=" . $url . $notificationUrl .
            "&merchantTransactionId=" . $unique_id;
        foreach ($tokens as $key => $value) {
            $data .= "&registrations[$key].id=" . $value;
        }
//        $data .= "&recurringType=REGISTRATION_BASED";

        if (env('HYPERPAY_TEST')) {
            $data .= "&testMode=EXTERNAL";
        }

        $url = env('HYPERPAY_URL', '') . "/v1/checkouts";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN', '')));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !env('HYPERPAY_TEST'));// this should be set to true in production
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        $responseData = curl_exec($ch);
        dd($responseData);
        if (curl_errno($ch)) {
            return [
                'values' => curl_error($ch),
                'response' => false
            ];
        }
        curl_close($ch);
        $values = json_decode($responseData);
        return [
            'values' => $values,
            'response' => true
        ];
    }


    public static function validateCheckoutRegistration($user, $money_log, $id)
    {

        $url = env('HYPERPAY_URL', '') . "/v1/checkouts/" . $id . "/registration";
        $url .= "?entityId=" . env('HYPERPAY_ENTITY_ID', '');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN', '')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        $values = json_decode($responseData);


        if (preg_match(self::SUCCESS_CODE_PATTERN, $values->result->code)
            || preg_match(self::SUCCESS_MANUAL_REVIEW_CODE_PATTERN, $values->result->code)) {
            self::register_credit_card($user->id, $values);
            $response = self::SUCCESS_RESPOSNE;
        } else if (preg_match(self::PENDING_HALF_PATTERN, $values->result->code)) {
            $response = self::PENDING_RESPONSE;
        } else {

            $response = self::FAILED_RESPONSE;
        }

        return [
            'code' => $response,
            'values' => $values
        ];
    }


    public static function validateCheckoutPayment($order, $money_log, $id, $type)
    {
        $url = env('HYPERPAY_URL', '') . "/v1/checkouts/" . $id . "/payment";
        $url .= "?entityId=" . env('HYPERPAY_ENTITY_ID', '');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN', '')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !env('HYPERPAY_TEST'));// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $values = json_decode($responseData);

        if (!preg_match(self::PENDING_HALF_PATTERN, $values->result->code)) {
            $new_hyperpay = new HyperpayResults();
            $new_hyperpay->type = $type;
            $new_hyperpay->item_id = $order->id;

            $new_hyperpay->payment_id = @$values->id ?: "";
            $new_hyperpay->payment_brand = @$values->paymentBrand ?: "";
            $new_hyperpay->transaction_id = @$values->merchantTransactionId ?: "";
            $new_hyperpay->amount = @$values->amount ?: "";
            $new_hyperpay->result = @json_encode($values->result) ?: "";
            $new_hyperpay->result_details = @json_encode($values->resultDetails) ?: "";
            $new_hyperpay->card = @json_encode($values->card) ?: "";
            $new_hyperpay->customer = @json_encode($values->customer) ?: "";
            $new_hyperpay->custom_parameters = @json_encode($values->customParameters) ?: "";
            $new_hyperpay->save();
        }

        if (preg_match(self::SUCCESS_CODE_PATTERN, $values->result->code) ||
            preg_match(self::SUCCESS_MANUAL_REVIEW_CODE_PATTERN, $values->result->code)) {
            self::register_credit_card($order->user_id,$values);

            $order->status=2;
            $new_hyperpay->is_success=1;
            $new_hyperpay->save();
            $money_log->status=1;
            $money_log->save();
            $order->payment=1;
            $order->save();

//
//            try {
//                $view='emails.client_emails.deliver_service_cardV2';
//
//                @\Illuminate\Support\Facades\Mail::send($view, ['order' => $order], function ($m) use ($order) {
//                    $m->from('info@mrmandoob.com', 'Mr.mandoob');
//                    $m->to(@$order->getUser->email, @$order->getUser->username)->subject("إشعار بتسليم طلب ");
//                });
//
////                    \Illuminate\Support\Facades\Mail::send('emails.client_emails.deliver_service', ['order' => Orders::find($order->id)], function ($m) use ($order)  {
////                        $m->from('info@mrmandoob.com', 'Mr.mandoob');
////                        $m->to(@$order->getUser->email, @$order->getUser->email)->subject("إشعار بتسليم طلب ");
////                    });
//            }catch (\Exception $ex){
//            }
            $response = self::SUCCESS_RESPOSNE;
        } else if (preg_match(self::PENDING_HALF_PATTERN, $values->result->code)) {
            $response = self::PENDING_RESPONSE;
        } else {
            if ($order){
                $order->payment_id = '';
                $order->save();
            }
            $response = self::FAILED_RESPONSE;
        }

        return [
            'code' => $response,
            'values' => $values
        ];
    }
    public static function deleteSavedCredit( $token)
    {

//        if (env('HYPERPAY_TEST')) {
//            $data .= "&testMode=INTERNAL";
//        }

        $url = env('HYPERPAY_URL', '') . "/v1/registrations/".$token;
        $url .= "?entityId=".env('HYPERPAY_ENTITY_ID', '');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN', '')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !env('HYPERPAY_TEST'));// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return [
                'values' => curl_error($ch),
                'response' => false
            ];
        }
        curl_close($ch);
        $values = json_decode($responseData);
        return [
            'values' => $values,
            'response' => true
        ];
    }
    public static function register_credit_card($user_id = 0, $values)
    {
//        Log::alert($user_id);
//        Log::alert($values);
        if (@$values->id && !UsersCreditCards::where('user_id', $user_id)->where('last4digits', @$values->card->last4Digits)->where('month', @$values->card->expiryMonth)->where('year', @$values->card->expiryYear)->first()) {
            $new_credit_card = new UsersCreditCards();
            $new_credit_card->user_id = $user_id;
            $new_credit_card->token = $values->id;
            $new_credit_card->last4digits = @$values->card->last4Digits;
            $new_credit_card->month = @$values->card->expiryMonth;
            $new_credit_card->year = @$values->card->expiryYear;
            $new_credit_card->payment_brand = @$values->paymentBrand ?: "";
            $new_credit_card->save();
//            Log::alert($new_credit_card->id);
        }
    }
}
