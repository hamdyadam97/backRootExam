<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends BaseController
{
    public function paymentTypes() {
        $paymentType = PaymentType::where('status', 1)->get();
        $paymentType->makeHidden('status');
        return $this->send_response('Payment Types',$paymentType);
    }
}
