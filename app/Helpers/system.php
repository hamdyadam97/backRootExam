<?php

use Twilio\Rest\Client as TwilioClient;
use Twilio\Exceptions\ConfigurationException;
use Illuminate\Http\Request;


function sendTwilioSms($message, $recipients)
{
    $account_sid = config("services.twilio.TWILIO_SID");
    $auth_token = config("services.twilio.TWILIO_AUTH_TOKEN");
    $twilio_number = config("services.twilio.TWILIO_NUMBER");
    $recipients = (int)$recipients;
//    dd($recipients);
    try {
        $client = new TwilioClient($account_sid, $auth_token);
        $client->messages->create(
            ("+" . $recipients),
            [
                'from' => $twilio_number,
                'body' => $message,

            ]);

    } catch (ConfigurationException $e) {
        return $e->getMessage();
    } catch (TypeError $e) {
        return $e->getMessage();
    }

}

function sendTwilioWhatsapp($message, $recipients)
{
    $account_sid = config("services.twilio.TWILIO_SID");
    $auth_token = config("services.twilio.TWILIO_AUTH_TOKEN");
    $whatsapp_number = config("services.twilio.TWILIO_WHATSAPP_NUMBER");
    $recipients = (int)$recipients;
//    dd($recipients);
    try {
        $client = new TwilioClient($account_sid, $auth_token);
        $x = $client->messages->create(
            ("whatsapp:+" . $recipients),
            [
                'from' => $whatsapp_number,
                'body' => $message,

            ]);
        return $x->status;
    } catch (ConfigurationException $e) {
        return $e->getMessage();
    } catch (TypeError $e) {
        return $e->getMessage();
    }

}


function stringNumberToInteger($string)
{
    return strtr($string, array('۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'));
}

function reArrangeTeleInputData(Request $request)
{
    $request['mobile_number'] = ltrim(stringNumberToInteger(str_replace(' ','',$request->mobile_number)), '0');
    $request['dial_code'] = ltrim(stringNumberToInteger($request->dial_code), '+');
    $request['mobile'] = $request['dial_code'] . $request['mobile_number'];

    return $request;
}

function isTest()
{
    return \request()->getHost() == "127.0.0.1";
}

function getPageNumber($url) {
    // Parse the URL to get the query string
    $urlComponents = parse_url($url);
    parse_str($urlComponents['query'], $queryParams);

    // Get the 'start' and 'length' parameters
    $start = isset($queryParams['start']) ? $queryParams['start'] : 0;
    $length = isset($queryParams['length']) ? $queryParams['length'] : 10; // Default to 10 if 'length' is not set
//    dd($start);

    // Calculate the page number
    $pageNumber = ($start / $length) + 1;

    return $pageNumber;
}
