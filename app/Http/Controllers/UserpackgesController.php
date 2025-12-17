<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Userpackges;
use App\Models\Packges;
use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserpackgesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::query()
            ->where('role_type', 2)
//            ->where('status', 1)
            ->get()->map(function ($user) {
                return [
                    'id' => $user['id'],
                    'full_name' => $user['first_name'] . ' ' . $user['last_name'],
                ];
            });

        $packages = Packges::query()->where('deleted_at', null)
            ->where('status', 1)->get();

        return view('userpackage.index', compact('users', 'packages'));
    }

    public function get(Request $request)
    {

        $data = Userpackges::query()->with(['user', 'getPackage', 'getHyperPay'])
            ->orderByDesc('id');


        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('coupon', function ($row) {
                return @$row->getHyperPay->coupon;
            })
            ->editColumn('start_date', function ($row) {
                return getDateFormateView($row->start_date);
            })
            ->editColumn('end_date', function ($row) {
                return getDateFormateView($row->end_date);
            })
            ->editColumn('username', function ($row) {
                $user = $row->user;
                return @$user->first_name . ' ' . @$user->last_name;
            })
            ->editColumn('packgename', function ($row) {
                $package = $row->getPackage;
                return @$package->name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    if ((bool)strtotime($search)) {
                        $instance->whereRaw("DATE_FORMAT(user_packages.start_date, '%d.%m.%Y') LIKE '%{$search}%'")
                            ->whereRaw("DATE_FORMAT(user_packages.end_date, '%d.%m.%Y') LIKE '%{$search}%'");
                    } else {
                        $instance->whereHas('user', function ($query) use ($search) {
                            $query->where(function ($query) use ($search) {
                                $query->where('first_name', 'LIKE', "%{$search}%")
                                    ->orWhere('last_name', 'LIKE', "%{$search}%");
                            });
                        })->orWhereHas('getPackage', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%{$search}%");
                        });
                    }
                }

                if (!empty($request->package_id)) {
                    $instance->where('user_packages.package_id', $request->package_id);
                }
            })
            ->escapeColumns([])
            ->make(true);

    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            if ($request->id) {
                $rules['user_id'] = 'required';
                $rules['package_id'] = 'required';
                $rules['start_date'] = 'required|date';
                $rules['end_date'] = 'required|date|after:start_date';
            } else {
                $rules['user_id'] = 'required';
                $rules['package_id'] = 'required';
                $rules['start_date'] = 'required|date';
                $rules['end_date'] = 'required|date|after:start_date';
            }

            $messsages = array(
                'user_id.required' => "Please select User.",
                'package_id.required' => "Please select package.",
                'start_date.required' => "Please select start date.",
                'start_date.date' => 'Please enter a valid start date.',
                'end_date.required' => "Please select end date.",
                'end_date.date' => 'Please enter a valid end date.',
                'end_date.after' => 'The end date must be after the start date.',
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('User package added successfully');
                if ($request->id) {
                    $model = Userpackges::where('id', $request->id)->first();
                    if ($model) {
                        $userpackge = $model;
                        $succssmsg = trans('User package updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $userpackge = new Userpackges;
                }
                $userpackge->user_id = $request->user_id;
                $userpackge->package_id = $request->package_id;
                $userpackge->start_date = ($request->start_date) ? date('Y-m-d', strtotime($request->start_date)) : NULL;
                $userpackge->end_date = ($request->end_date) ? date('Y-m-d', strtotime($request->end_date)) : NULL;
                if ($userpackge->save()) {
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                    DB::transaction(function () use ($userpackge) {

                    Invoice::firstOrCreate(
                        ['user_package_id' => $userpackge->id],
                        [
                            'user_id' => $userpackge->user_id,
                            'invoice_number' => 'INV-' . now()->format('YmdHis'),
                            'total_amount' => $userpackge->getPackage->price,
                            'status' => 'draft',
                            'sent_to_accounting' => 0
                        ]
                    );

                });

                } else {
                    $result = ['status' => false, 'message' => 'Error in saving data', 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $userpackge = Userpackges::query()->find($request->id);
            $userpackge->start_date = date('d.m.Y', strtotime($userpackge->start_date));
            $userpackge->end_date = date('d.m.Y', strtotime($userpackge->end_date));
            $result = ['status' => true, 'message' => '', 'data' => $userpackge];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $userpackge = Userpackges::where('id', $request->id)->first();

        if ($userpackge->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}



