<?php

namespace App\Console\Commands;


use App\Helpers\HyperPay;
use App\Models\DiscountsCode;
use App\Models\HyperpayResults;
use App\Models\MoneyLogs;
use App\Models\Packges;
use App\Models\Userpackges;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConfirmPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ConfirmPayments:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ConfirmPayments:cron.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $objects=MoneyLogs::where('status',0)->whereBetween('created_at',[Carbon::now()->subHours(5),Carbon::now()])
            ->get();

        foreach ($objects as $object){
            Log::alert('cron callback payment:'.$object->id);
            $money_log = MoneyLogs::where('payment_id',$object->payment_id)->first();
            $user=User::find($money_log->user_id);
            $package = Packges::find($money_log->item_id);
            if(!$money_log || !$package || !$user){
                continue;
                return $this->send_error('Sorry This payment not has package', null, 400);
            }
            if($money_log->status==1){
                continue;
                return $this->send_response('Successful payment', null, 200);
            }


            $response = HyperPay::validateCheckoutRegistration($user , $money_log , $object->payment_id );
            $values = $response['values'];
            if ($response['code'] === HyperPay::SUCCESS_RESPOSNE){
                $new_hyperpay=HyperpayResults::where('user_id',$money_log->user_id)
                    ->where('item_id',$money_log->item_id)
                    ->where('payment_id',$object->payment_id)
                    ->whereBetween('created_at',[Carbon::now()->subHours(5),Carbon::now()])
                    ->first();
                if(!$new_hyperpay){
                    $new_hyperpay = new HyperpayResults();
                }
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
                $new_hyperpay->custom_parameters = @json_encode($values->customParameters) ?: "";
                $new_hyperpay->is_success =1;
                $new_hyperpay->save();

                $money_log->status=1;
                $money_log->save();
                $package=Packges::find($money_log->item_id);
                $subscribe = new Userpackges;
                $subscribe->user_id = $money_log->user_id;
                $subscribe->package_id = $money_log->item_id;

                $discount=0;
                if($money_log->coupon!=null){
                    $getCoupon=DiscountsCode::where('code',$money_log->coupon)
                        ->first();
                    if($package->type_id == 1){
                        $price=($package->price/100)*($getCoupon->percentage);
                        $discount=$package->price-$price;
                    }else{
                        $discount= $package->price - $getCoupon->amount ;
                        if($discount<0){
                            $price=$package->price;
                            $discount=$package->price-$price;
                        }
                    }
                }
                $subscribe->price=$new_hyperpay->amount;
                $subscribe->price_before_discount=$new_hyperpay->amount+$discount;
                $subscribe->discount=$discount;
//                    start date and end date get from period
                $subscribe->start_date = Carbon::now();
                $subscribe->end_date =Carbon::now()->addDays(intval($package->period));
                $subscribe->save();


//                return $this->send_response('Successful payment', ['data'=>[$values],'package_id'=>[$money_log->item_id]], 200);

            }else if ($response['code'] === HyperPay::PENDING_RESPONSE){
//                return $this->send_error('Waiting for payment', null, 400);

            }else {
                $money_log->status=2;
                $money_log->save();
//                return $this->send_error( @$values->result->description . " ( " . @$values->result->code . " ) " ,['data'=>[$values]], 400);


            }

        }

    }
}
