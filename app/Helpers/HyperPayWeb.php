<?php

namespace App\Helpers;

// Import the class namespaces first, before using it directly


use App\Http\Resources\OrderNotificationResource;
use App\Models\HyperpayResults;
use App\Models\Notification;
use App\Models\Settings;
use App\Models\UsersCreditCards;
use Illuminate\Support\Facades\Log;

class HyperPayWeb
{
//    const SUCCESS_CODE_PATTERN = '/^(000\.000\.|000\.100\.1|000\.[36])/';
//    const SUCCESS_MANUAL_REVIEW_CODE_PATTERN = '/^(000\.400\.0[^3]|000\.400\.[0-1]{2}0)/';
//    const PENDING_HALF_PATTERN = '/^(000\.200)/';

    const SUCCESS_CODE_PATTERN = '/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/';
    const SUCCESS_MANUAL_REVIEW_CODE_PATTERN = "/^(000.400.0[^3]|000.400.100)/";
    const PENDING_HALF_PATTERN = '/^(000\.200)/';


    const SUCCESS_RESPOSNE = 1;
    const PENDING_RESPONSE = 2;
    const FAILED_RESPONSE = 0;

    public const VISA = 'visa';
    public const MADA = 'mada';


    public static function pay($credit, $type, $billing = [], $merchantTransactionId = null,$tokens=null,$discount)
    {
//        $auth_key = self::AUTH_KEY;
        $auth_key = env('HYPERPAY_ACCESS_TOKEN', '');
        $entity_id = ((strtolower($type) === self::VISA) ? env('HYPERPAY_ENTITY_ID') : env('HYPERPAY_MADA_ENTITY_ID'));

        $hyperpayUrl = env('HYPERPAY_URL', '');
        $url = $hyperpayUrl . "/v1/checkouts";

        $price=$credit-$discount;
        $price = number_format($price,2,'.','');

        $data = "entityId=" . $entity_id .
            "&amount=" . $price .
            "&currency=JOD" .
            "&paymentType=" . 'DB' .
            "&merchantTransactionId=" . $merchantTransactionId;

        foreach ($tokens as $key => $value) {
            $data .= "&registrations[$key].id=" . $value;
        }
//        $data .= "&recurringType=REGISTRATION_BASED";

        if (env('HYPERPAY_TEST')) {
//            $data .= "&testMode=EXTERNAL";
            $data .= "&customParameters[3DS2_enrolled]=" . 'true';
        }

        foreach ($billing as $key => $item) {
            $data .= "&" . $key . "=" . $item;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !env('HYPERPAY_TEST'));// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $res = json_decode($responseData, true);
        return $res;
    }


    public static function validateCheckout($id, $payment_type, $get_amount = false)
    {

        $url = env('HYPERPAY_URL');
        $url .= '/v1/checkouts/' . $id . '/payment';
         $url .= "?entityId=" . ((strtolower($payment_type) === self::VISA) ? env('HYPERPAY_ENTITY_ID') : env('HYPERPAY_MADA_ENTITY_ID'));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !env('HYPERPAY_TEST'));// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($responseData, true);

        if (isset($response['ndc']) && $response['ndc'] === $id
            && isset($response['result']) && isset($response['result']['code']) &&
            (preg_match(self::SUCCESS_CODE_PATTERN, $response['result']['code'])
                || preg_match(self::SUCCESS_MANUAL_REVIEW_CODE_PATTERN, $response['result']['code'])
                || $response['result']['code'] === '000.200.100')
            && isset($response['currency']) && isset($response['amount'])
            && $response['currency'] === 'JOD') {
            if ($get_amount) {
                return [true, $response['amount'], $response['result']['description'],$response];
            }
            return true;
        } else {
            return [false,$response['result']['code'],$response['result']['description']];
        }
    }



    public static function register_credit_card($user_id = 0, $values)
    {
//        Log::alert($user_id);
//        Log::alert($values);
        if (@$values->id && !UsersCreditCards::where('user_id', $user_id)
                ->where('last4digits', @$values['card']['last4Digits'])
                ->where('month', @$values['card']['expiryMonth'])->where('year', @$values['card']['expiryYear'])->first()) {
            $new_credit_card = new UsersCreditCards();
            $new_credit_card->user_id = $user_id;
            $new_credit_card->token = $values->id;
            $new_credit_card->last4digits = @$values['card']['last4Digits'];
            $new_credit_card->month = @$values['card']['expiryMonth'];
            $new_credit_card->year = @$values['card']['expiryYear'];
            $new_credit_card->payment_brand = @$values->paymentBrand ?: "";
            $new_credit_card->save();
//            Log::alert($new_credit_card->id);
        }
    }
}
