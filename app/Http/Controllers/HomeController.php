<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator, Redirect, Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        if (Auth::user()) {
            $loginUser = Auth::user();
            $roles = User::$role;
            return view('home', compact('loginUser', 'roles'));
        } else {
            return redirect()->route('login');
        }
    }

    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function set_new_password($token, Request $request)
    {
        $user = User::where('password_token', $token)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('user.resetpassword', compact('token'));

        // $user=User::select('password_token');
    }

    public function confirm_new_password(Request $request)
    {

        $user = User::where('password_token', $request->password_token)->first();
        if (!$user) {
            return redirect()->route('login');
        }
        $rules = array(
            'password_confirmation' => 'required',
            'password' => 'required|confirmed|min:8|string',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
        } else {
            $user->password = Hash::make($request->password);
            $user->password_token = null;
            if ($user->save()) {
                $msg = 'Password create successfully';
                Session::flash('success', $msg);

                return redirect()->route('login')->with('message', $msg);
            } else {
                return redirect()->back()->withErrors($user->getErrors())->withInput($request->input());
            }
        }


    }


    public function chech_efawatercom(Request $request)
    {

        $data = $request->all();
        \Log::info('e-fawatercom: '. json_encode($data));
        $array = [
            "MFEP" => [
                "MsgHeader" => [
                    "TmStp" => now()->format('Y-m-d\TH:i:s'),
                    "GUID" => @$data['MFEP']['MsgHeader']['GUID'],
                    "TrsInf" => [
                        "ResTyp" => "BILPULRS",
                        "SdrCode" => 1704
                    ],
                    "Result" => [
                        "ErrorDesc" => "Success",
                        "Severity" => "Info",
                        "ErrorCode" => 0
                    ],
                ],
                "MsgBody" => [
                    "BillRec" => [
                        [
                            "ServiceType" => "Package Subscription",
                            "DueAmount" => "4.000",
                            "BillType" => "OneOff",
                            "PmtConst" => [
                                "AllowPart" => false,
                                "Upper" => "4.000",
                                "Lower" => "4.000"
                            ],
                            "IssueDate" => now()->format('Y-m-d\TH:i:s'),
                            "BillStatus" => "BillNew",
                            "AcctInfo" => [
                                "BillNo" => "126",
                                "BillingNo" => "126"
                            ],
                            "AdditionalInfo" => [
                                "CustName" => "Hani Abo Arab",
                                "FreeText" => "this is free text"
                            ],
                            "DueDate" => now()->addMinutes(30)->format('Y-m-d\TH:i:s')
                        ]
                    ]
                ],
                "RecCount" => 1
            ]
        ];


        return response()->json($array);
    }


}
