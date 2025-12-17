<?php

namespace App\Http\Controllers;

use App\Models\DiscountsCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountsCodeController extends Controller
{
    public function index()
    {
        return view('discountscode.index');
    }

    public function get(Request $request)
    {
        $data = DiscountsCode::query()
            ->withCount('moneyLogs');
//            ->withCount('hyperPays');
//            ->get();
//            ->select('*');
//dd($data->get());
        return datatables()::of($data)
            ->editColumn('type', function($row) {
                if($row->type == 1) {
                    return "Percentage";
                } else {
                    return "Amount";
                }
            })
            ->editColumn('status', function($row) {
                if($row->status == 1) {
                    return "<h5><span class='badge bg-primary'>Active</span></h5>";
                } else {
                    return "<h5><span class='badge bg-danger'>Deactive</span></h5>";
                }
            })
            ->addColumn('hyper_pays_count', function($row) {
                return $row->money_logs_count?:0;
//                return $row->hyper_pays_count?:0;
            })
            ->editColumn('from_date', function($row) {
                return date('d.m.Y', strtotime($row->from_date));
            })
            ->editColumn('to_date', function($row) {
                return date('d.m.Y', strtotime($row->to_date));
            })
            ->addColumn('discount', function($row) {
                if($row->type == 1) {
                    return $row->percentage;
                } else {
                    return $row->amount;
                }
            })
            ->addColumn('action', function($row) {

                $action = "";
                $action .= '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';
                $action .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="waves-effect waves-light pe-2 edit-row" title="edit"><i class="bx bx-edit-alt bx-sm"></i></a>';
                $action .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="waves-effect waves-light text-danger pe-2 delete-row" title="delete"><i class="bx bx-trash bx-sm"></i></a>';
                $action .= '</div>';

                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action','discount','status'])
            ->make(true);
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {

            if ($request->id) {
                $rules['code'] = 'required|unique:discounts_codes,code,'.$request->id;
                $rules['marketer'] = 'required';

                if($request->type == 1) {
                    $rules['percentage'] = 'required|numeric';
                }else {
                    $rules['amount'] = 'required|numeric';
                }

                $rules['quantity'] = 'required|numeric';
                $rules['from_date'] = 'required';
                $rules['to_date'] = 'required';
                $rules['status'] = 'required';
            } else {
                $rules['code'] = 'required|unique:discounts_codes,code';
                $rules['marketer'] = 'required';

                if($request->type == 1) {
                    $rules['percentage'] = 'required|numeric';
                }else {
                    $rules['amount'] = 'required|numeric';
                }

                $rules['quantity'] = 'required|numeric';
                $rules['from_date'] = 'required';
                $rules['to_date'] = 'required';
            }

            $messsages = array(
                'code.required' => "The code field is required.",
                'marketer.required' => "The marketer field is required.",
                'percentage.required' => "The percentage field is required.",
                'amount.required' => "The amount field is required.",
                'quantity.required' => "The quantity field is required.",
                'from_date.required' => "The from date field is required.",
                'to_date.required' => "The to date field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);

            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                if($request->id) {
                    $model = DiscountsCode::find($request->id);

                    if($model) {
                        $discountcode = $model;
                        $succssmsg = trans('Discount code updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $succssmsg = trans('Discount code added successfully');
                    $discountcode = new DiscountsCode();
                }

                $discountcode->code = $request->code;
                $discountcode->marketer = $request->marketer;
                $discountcode->type = $request->type;
                $discountcode->percentage = $request->percentage ?? null;
                $discountcode->amount = $request->amount ?? null;
                $discountcode->quantity = $request->quantity;
                $discountcode->from_date = ($request->from_date) ? date('Y-m-d', strtotime($request->from_date)) : null;
                $discountcode->to_date = ($request->to_date) ? date('Y-m-d', strtotime($request->to_date)) : null;
                $discountcode->status = $request->status;

                if ($discountcode->save()) {
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
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
            $discountcode = DiscountsCode::find($request->id);
            $discountcode->from_date = date('d.m.Y', strtotime($discountcode->from_date));
            $discountcode->to_date = date('d.m.Y', strtotime($discountcode->to_date));
            $result = ['status' => true, 'message' => '', 'data' => $discountcode];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $discountcode = DiscountsCode::find($request->id);

        if ($discountcode->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }

        return response()->json($result);
    }
}
