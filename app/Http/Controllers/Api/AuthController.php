<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\Api\UserResource;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    // user signup api
    public function signup(Request $request)
    {
        DB::beginTransaction();
        reArrangeTeleInputData($request);

        try {
//                    |digits:10
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'mobile' => 'required|numeric|unique:users,mobile',
                'password' => 'required|min:8|confirmed',
                'email' => 'required|email|unique:users,email',
                'specialization' => 'required|string|max:255',
                'governorate' => 'required|string|max:255',
                'birth_date' => 'required|date',
            ]);
            if ($validator->fails()) {
                return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
            }

            $otp = rand(100000, 999999);

            $user = User::query()->create([
                'first_name' => $request['name'],
                'mobile_country_code' => $request['mobile_country_code'],
                'dial_code' => $request['dial_code'],
                'mobile_number' => $request['mobile_number'],
                'mobile' => $request['mobile'],
                'specialization' => $request->specialization,
                'email' => $request->email,
                'governorate' => $request->governorate,
                'birth_date' => $request->birth_date,
                'otp' => $otp,
                'password' => Hash::make($request->password),
                'profile_completed' => true,

            ]);

            $this->send_otp($request['mobile'], $otp);
            $success['user'] = new UserResource($user);

            DB::commit();
            return $this->send_response('User details saved successfully', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);

        }

    }

    public function verifyotp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
            'otp' => 'required|numeric|digits:6',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {
            $user = User::query()->where('mobile', $request->mobile)->first();
            if (!$user) {
                return $this->send_error('Validation Errors', ['mobile' => ['Invalid Mobile number.']], 422);
            }

            if ($user->otp != $request->otp) {
                return $this->send_error('Validation Errors', ['otp' => ['Invalid OTP']], 422);
            }

            $user->update([
                'mobile_verified_at' => now(),
                'otp' => null
            ]);

            $token = $user->createToken('Exammanagement');
            $user->makeTrailSubscription();

            $success['token'] = $token->accessToken;
            $success['token_expired_at'] = $token->token->expires_at;
            $success['user'] = new UserResource($user);

            DB::commit();
            return $this->send_response('OTP Verification Successfully', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }
    }

    public function resendotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {
            $user = User::query()->where('mobile', $request->mobile)->first();

            if (!$user) {
                return $this->send_error('Validation Errors', ['mobile' => ['Invalid Mobile number.']], 422);
            }

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->save();

            $this->send_otp($user->mobile, $otp);
            $success['user'] = new UserResource($user);

            DB::commit();
            return $this->send_response('OTP Send Successfully', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }
    }

    public function checkVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {
            $user = User::query()->where('mobile', $request->mobile)->first();

            if (!$user) {
                return $this->send_error('Validation Errors', ['mobile' => ['Invalid Mobile number.']], 422);
            }

            if (isset($user->mobile_verified_at)) {
                $token = $user->createToken('Exammanagement');
                $user->makeTrailSubscription();

                $success['token'] = $token->accessToken;
                $success['token_expired_at'] = $token->token->expires_at;
                $success['user'] = new UserResource($user);
            }else{
                $success['user'] = ['mobile' => $user->mobile];
                $success['go_to_verify'] = true;
            }


            DB::commit();
            return $this->send_response('success', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }
    }


    // user login api
    public function login(Request $request)
    {
        $dialCode = ltrim(stringNumberToInteger(trim($request->get('dial_code'))), '+');
        $request['mobile'] = $dialCode . ltrim(stringNumberToInteger(trim($request->get('mobile'))), '0');

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {

            if (Auth::attempt(['mobile' => $request->mobile, 'password' => $request->password])) {
                $user = Auth::user();

                if (!$user->mobile_verified_at) {
                    $otp = rand(100000, 999999);
                    $user->otp = $otp;
                    $user->save();

                    $this->send_otp($user->mobile, $otp);
                    $success['user'] = ['mobile' => $user->mobile];
                    $success['go_to_verify'] = true;


                    DB::commit();
                    return $this->send_response('OTP Send Successfully', $success);

                }
                $token = $user->createToken('Exammanagement');
                $success['token'] = $token->accessToken;
                $success['token_expired_at'] = $token->token->expires_at;
                $success['user'] = new UserResource($user);
                DB::commit();
                return $this->send_response('Login Successfully', $success);
            } else {
                return $this->send_error('Unauthorised', ['WrongCredentials' => ['Mobile number or password is wrong. Please check your credentials.']], 422);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }

    }

    public function forget(Request $request)
    {
        reArrangeTeleInputData($request);

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {

            $user = User::query()->where('mobile', $request->mobile)->first();

            if (!$user) {
                return $this->send_error('Validation Errors', ['mobile' => ['Invalid Mobile number.']], 422);
            }

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->save();

            $this->send_otp($user->mobile, $otp);
            $success['user'] = new UserResource($user);

            DB::commit();
            return $this->send_response('OTP Send Successfully', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }
    }

    public function verifyOtpForForget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
            'otp' => 'required|numeric|digits:6',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {
            $user = User::query()->where('mobile', $request->mobile)->first();

            if (!$user) {
                return $this->send_error('Validation Errors', ['mobile' => ['Invalid Mobile number.']], 422);
            }

            if ($user->otp != $request->otp) {
                return $this->send_error('Validation Errors', ['otp' => ['Invalid OTP']], 422);
            }

            $user->update([
                'otp' => null
            ]);
            $success['user'] = new UserResource($user);

            DB::commit();
            return $this->send_response('OTP Verification Successfully', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }
    }

    public function reset(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|exists:users,mobile',
            'password' => 'required|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        DB::beginTransaction();
        try {

            $user = User::query()->where('mobile', $request->mobile)->first();

            if (!$user) {
                return $this->send_error('Validation Errors', ['mobile' => ['Invalid Mobile number.']], 422);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            $success['user'] = ['id' => $user->id, 'mobile' => $user->mobile];
            $token = $user->createToken('Exammanagement');
            $success['token'] = $token->accessToken;
            $success['token_expired_at'] = $token->token->expires_at;
            $success['user'] = new UserResource($user);

            DB::commit();
            return $this->send_response('Reset Password Done Successfully', $success);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
        }
    }


    public function send_otp($mobile, $otp)
    {
        $otpmessage = "Your OTP of reset password is : " . $otp;
        $sms_response = sendsmsReleans($mobile, $otpmessage);
        return $sms_response;
    }

//    public function sendotptest(Request $request)
//    {
//
//        $otpmessage = "Your OTP of reset password is : 1234";
//        $mobile = "00962796288623";
//        $otpmessage = urlencode($otpmessage);
//
//        $sms_response = sendsms($mobile, $otpmessage);
//
//        print_r($sms_response);
//
//    }


    public function resetpassword(Request $request)
    {
        // echo env('SMS_TO_API_KEY'); die;

        $rules = array(
            'user_id' => 'required|numeric|exists:users,id',
            'new_password' => 'required|string',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        $user = User::where('id', $request->user_id)->first();
        if (empty($user)) {
            return $this->send_error('No user found', ['PasswordError' => ['User does not exist']], 400);
        }
        $user_id = $user->id;
        $user->password = Hash::make($request->new_password);
        $user->otp = NULL;
        $user->save();
        return $this->send_response('Password changed successfully', ['user' => $user]);
    }

//    public function registerOtp(Request $request)
//    {
//        $rules = array(
//            'mobile' => 'required|digits:10|numeric|unique:users,mobile',
//        );
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
//        }
//
//        $otp = rand(100000, 999999);
//        $mobile = $request->mobile;
//        $otpmessage = "Your OTP for registration with " . env('APP_NAME') . " is : " . $otp;
//
//        $mobileOtpRequest = new MobileOtpRequestLog();
//        $mobileOtpRequest->mobile = $mobile;
//        $mobileOtpRequest->otp = $otp;
//
//        if ($mobileOtpRequest->save()) {
//
//            if (substr($mobile, 0, 1) == "0")
//                $mobile = substr($mobile, 1);
//
//            if (strlen($mobile) == 9)
//                $mobile = "966" . $mobile;
//
//            $sms_response = sendsms($mobile, $otpmessage);
//            $success['request_otp'] = ['sms_response' => $sms_response];
//
//            return $this->send_response('OTP Send Successfully', $success);
//        } else {
//            return $this->send_error('Mobile Otp not saved', ['MobileOtpNotSaved' => ['Something went wrong while saving otp request']], 400);
//        }
//
//    }

//    public function register(Request $request)
//    {
//        $rules = array(
//            'mobile' => 'required|digits:10|numeric|exists:mobile_otp_request_logs,mobile',
//            'otp' => 'required|numeric|digits:6',
//            'password' => 'required|string',
//        );
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
//        }
//
//        $getOtp = MobileOtpRequestLog::where('mobile', $request->mobile)->latest('created_at')->first();
//
//        if ($getOtp->otp != $request->otp) {
//            return $this->send_error('Validation Errors', ['otp' => ['Invalid OTP']], 422);
//        }
//
//        $user = new User;
//        $user->mobile = $request->mobile;
//        $user->password = Hash::make($request->password);
//        if (!$user->save()) {
//            return $this->send_error('User details not saved', ['UserDetailsNotSaved' => ['Something went wrong while saving user data']], 400);
//        }
//
//        MobileOtpRequestLog::where('mobile', $request->mobile)->each(function ($q) {
//            $q->delete();
//        });
//
//        $subscribe = new Userpackges;
//        $subscribe->user_id = $user->id;
//        $subscribe->package_id = 1;
//        $subscribe->start_date = date('Y-m-d');
//        $subscribe->end_date = date('Y-m-d', strtotime('+90 days'));
//        $subscribe->save();
//
//        $token = $user->createToken('Exammanagement');
//        $success['token'] = $token->accessToken;
//        $success['token_expired_at'] = $token->token->expires_at;
//        $success['mobile'] = $user->mobile;
//        return $this->send_response('User details saved successfully', $success);
//    }


//    public function registerOtpUsingWhatsapp(Request $request)
//    {
//        $rules = array(
//            'mobile' => 'required|digits:10|numeric|unique:users,mobile',
//        );
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
//        }
//
//        $otp = rand(100000, 999999);
//        $mobile = $request->mobile;
//        $otpmessage = "Your OTP for registration with " . env('APP_NAME') . " is : " . $otp;
//
//        $mobileOtpRequest = new MobileOtpRequestLog();
//        $mobileOtpRequest->mobile = $mobile;
//        $mobileOtpRequest->otp = $otp;
//
//        if ($mobileOtpRequest->save()) {
//
//            if (substr($mobile, 0, 1) == "0")
//                $mobile = substr($mobile, 1);
//
//            if (strlen($mobile) == 9)
//                $mobile = "966" . $mobile;
//
//            $sms_response = sendOtpThroughWhatsapp($mobile, $otpmessage);
//            $success['request_otp'] = ['sms_response' => $sms_response];
//
//            return $this->send_response('OTP Send Successfully', $success);
//        } else {
//            return $this->send_error('Mobile Otp not saved', ['MobileOtpNotSaved' => ['Something went wrong while saving otp request']], 400);
//        }
//    }

}
