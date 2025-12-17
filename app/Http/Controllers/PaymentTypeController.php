<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentTypeController extends Controller
{
    public function index()
    {
        return view('payment_type.index');
    }

    public function get(Request $request)
    {
        $data = PaymentType::all();

        return datatables()::of($data)
        ->editColumn('status', function($row) {
            if($row->status == 1) {
                return "<h5><span class='badge bg-primary'>Active</span></h5>";
            } else {
                return "<h5><span class='badge bg-danger'>Deactive</span></h5>";
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
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {

            $rules['payment_type'] = 'required';

            $messsages = array(
                'payment_type.required' => "The Payment Type field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);

            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                if($request->id) {
                    $model = PaymentType::find($request->id);

                    if($model) {
                        $paymentType = $model;
                        $succssmsg = trans('Payment Type updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $succssmsg = trans('Payment Type added successfully');
                    $paymentType = new PaymentType();
                }

                $paymentType->payment_type = $request->payment_type;
                $paymentType->status = $request->status;

                if ($paymentType->save()) {
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
            $paymentType = PaymentType::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $paymentType];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $discountcode = PaymentType::find($request->id);

        if ($discountcode->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }

        return response()->json($result);
    }
}
