<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\User;
use Illuminate\Http\Request;
use App\Helpers\MailerFactory;
use Illuminate\Support\Carbon;
use Validator, Redirect, Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->middleware('guest');
        $this->mailer = $mailer;
    }
    public function register()
    {
        $countries = User::$countries;
        return view('auth.register', compact('countries'));
    }

    public function store(Request $request)
    {
        $role_type = (isset($request->role_type)) ? $request->role_type : 3;

        $contract_startdate = $contract_enddate = null;
        if ($role_type == 2) {
            $rules = array(
                'company_name' => 'required|string|max:100',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'specialization' => 'required|string|max:100',
                'governorate' => 'required|string|max:100',
                'street' => 'required',
                'house_number' => 'required|string|max:10',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'vat_number' => 'required|string|max:50',
                'shop_start_time' => 'required',
                'shop_end_time' => 'required|after:shop_start_time',
                'email' => 'required|string|email|max:50|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'phone' => "required|string|min:10",
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
                // 'shop_address' => 'required|string|max:500',
                // 'document_file' => 'required',
                'bank_name' => 'required',
                'iban' => 'required',
                'bic' => 'required',
                'agbs_terms' => 'required',
                'dsgvo_terms' => 'required',
                'sepa_terms' => 'required',
            );

            $contract_startdate = Carbon::now()->format('Y-m-d');
            $contract_enddate  = Carbon::now()->addMonths(13)->format('Y-m-d');
        } else {
            $rules = array(
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'specialization' => 'required|string|max:100',
                'governorate' => 'required|string|max:100',
                'street' => 'required',
                'house_number' => 'required|max:10',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'email' => 'required|string|email|max:50|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'phone' => "required|string|min:10",
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
                'agbs_terms' => 'required',
                'dsgvo_terms' => 'required',
                'sepa_terms' => 'required',
                'birth_date' => 'required|before:18 years ago',
                'salutation'  => 'required',
                'customer_company_name' => 'required_if:salutation,==,firma|nullable|string|max:100',
                'customer_vat_number' => 'required_if:salutation,==,firma|nullable|string|max:50'
                // 'shop_address' => 'required|string|max:500',
            );
        }
        $messsages = array(
            // 'document_file.required' => "The Business Registration PDF is required.",
            // 'document_file.mimes' => "Please upload a valid pdf file",
            // 'phone.min' => "Please enter valid phone number.",
            // 'shop_start_time.date_format' => "Please enter valid shop start time.",
            // 'shop_end_time.date_format' => "Please enter valid shop end time.",
            // 'shop_end_time.after' => "The shop end time must be a time after shop start time.",
            // 'birth_date.before' => "You must be over 18 years old to become a customer.",
            // 'customer_company_name.required_if' => 'The company name field is required.',
            // 'customer_vat_number.required_if' => 'The vat number field is required.'
            // 'agbs_terms.required' => "Please accept AGBs terms and conditions",
            // 'dsgvo_terms.required' => "Please accept DSGVO terms and conditions",
        );
        $request->validate($rules, $messsages);

        $phone = str_replace(array("(", ")", "-", " "), array("", "", "", "", ""), $request->phone);

        // dd('start date: ' . $contract_startdate . 'end date: ' . $contract_enddate);

        // $document_file = $company_logo = NULL;
        // if ($request->hasFile('document_file') && $request->role_type == 2) {
        //     $filename = $request->document_file->hashName();
        //     $request->document_file->storeAs('document_file', $filename, 'public');
        //     $document_file = $filename;
        // }
        // if ($request->hasFile('company_logo') && $request->role_type == 2) {
        //     $filename = $request->company_logo->hashName();
        //     $request->company_logo->storeAs('company_logo', $filename, 'public');
        //     $company_logo = $filename;
        // }
        $user = User::create([
            
            'company_name' => ($request->company_name) ? $request->company_name : (($request->customer_company_name) ? $request->customer_company_name : NULL),
            'vat_number' => ($request->vat_number) ? $request->vat_number : (($request->customer_vat_number) ? $request->customer_vat_number : NULL),
            'street' => ($request->street) ? $request->street : NULL,
            'house_number' => ($request->house_number) ? $request->house_number : NULL,
            'zipcode' => ($request->zipcode) ? $request->zipcode : NULL,
            'city' => ($request->city) ? $request->city : NULL,
            'country' => ($request->country) ? $request->country : NULL,
            'bank_name' => ($request->bank_name) ? $request->bank_name : NULL,
            'iban' => ($request->iban) ? $request->iban : NULL,
            'bic' => ($request->bic) ? $request->bic : NULL,
            // 'shop_address' => ($request->shop_address) ? $request->shop_address : NULL,
            'shop_start_time' => ($request->shop_start_time) ? date('H:i', strtotime($request->shop_start_time)) : NULL,
            'shop_end_time' => ($request->shop_end_time) ? date('H:i', strtotime($request->shop_end_time)) : NULL,
            'role_type' => $request->role_type,
            'phone' => $phone,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contract_startdate' => $contract_startdate,
            'contract_enddate' => $contract_enddate,
            'birth_date' => ($request->birth_date) ? date('Y-m-d', strtotime($request->birth_date)) : NULL,
            'salutation' => ($request->salutation) ? $request->salutation : NULL,
            'specialization' => ($request->country) ? $request->specialization : NULL,
            'governorate' =>($request->country) ? $request->governorate : NULL,
            // 'document_file' => $document_file,
            // 'company_logo' => $company_logo,

            'status' => 0,
        ]);
        if ($user) {
            $this->mailer->sendWelcomeEmail($user);
        }
        return Redirect::to("login")->with('status', trans('translation.You_have_successfully_registered_waiting_for_your_approval_account'));
    }
}
