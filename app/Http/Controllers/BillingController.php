<?php

namespace App\Http\Controllers;
use App\Services\PhenixBillingService;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* =======================
       View
    ======================= */
    public function index()
    {
        return view('billing.invoices');
    }

    /* =======================
       Datatable
    ======================= */
    public function get(Request $request)
    {
        $data = Invoice::with(['user','userPackage.getPackage'])
            ->orderByDesc('id');

        return datatables()::of($data)
            ->addIndexColumn()

            ->editColumn('username', function ($row) {
                return @$row->user->first_name.' '.@$row->user->last_name;
            })

            ->editColumn('packagename', function ($row) {
                return @$row->userPackage->getPackage->name;
            })

            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount, 2);
            })

            ->editColumn('status', function ($row) {
                return ucfirst($row->status);
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <button class="btn btn-sm btn-info viewInvoice" data-id="'.$row->id.'">
                        <i class="bx bx-show"></i>
                    </button>
                    <button class="btn btn-sm btn-warning editInvoice" data-id="'.$row->id.'">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteInvoice" data-id="'.$row->id.'">
                        <i class="bx bx-trash"></i>
                    </button>
                ';
            })

            ->escapeColumns([])
            ->make(true);
    }

    /* =======================
       Add / Update
    ======================= */
    public function addupdate(Request $request)
    {
        if ($request->ajax()) {

            $rules = [
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required'
            ];

            $messages = [
                'total_amount.required' => 'Amount is required',
                'total_amount.numeric' => 'Amount must be numeric',
                'status.required' => 'Status is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ]);
            }

            DB::beginTransaction();
            try {

                if ($request->id) {
                    $invoice = Invoice::findOrFail($request->id);
                    $msg = 'Invoice updated successfully';
                } else {
                    $invoice = new Invoice();
                    $invoice->invoice_number = 'INV-' . now()->format('YmdHis');
                    $msg = 'Invoice created successfully';
                }

                $invoice->total_amount = $request->total_amount;
                $invoice->status = $request->status;
                $invoice->save();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => $msg
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return response()->json(['status' => false, 'message' => 'Invalid request']);
    }







    /* =======================
       Detail
    ======================= */
    public function detail(Request $request)
    {
        if ($request->ajax()) {

            $invoice = Invoice::with(['user','userPackage.getPackage'])
                ->find($request->id);

            if (!$invoice) {
                return response()->json(['status' => false]);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'user' => $invoice->user->first_name.' '.$invoice->user->last_name,
                    'package' => @$invoice->userPackage->getPackage->name,
                    'total_amount' => $invoice->total_amount,
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at->format('Y-m-d H:i'),
                ]
            ]);
        }

        return response()->json(['status' => false]);
    }

    /* =======================
       Delete
    ======================= */
    public function delete(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if ($invoice && $invoice->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'Invoice deleted successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Delete failed'
        ]);
    }



public function sendToSystem(Request $request, PhenixBillingService $phenix)
{
    $request->validate([
        'invoices' => 'required|array'
    ]);

    try {

        $invoices = Invoice::with(['user','userPackage.getPackage'])
            ->whereIn('id', $request->invoices)
            ->get();

        if ($invoices->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No invoices found'
            ], 404);
        }

        $responses = [];

        foreach ($invoices as $invoice) {

            $payload = [
                'InvoiceNumber' => $invoice->invoice_number,
                'CustomerName'  => $invoice->user->first_name . ' ' . $invoice->user->last_name,
                'Amount'        => $invoice->total_amount,
                'Description'   => optional($invoice->userPackage->getPackage)->name,
                'Date'          => $invoice->created_at->format('Y-m-d'),
            ];

            $response = $phenix->sendInvoice($payload);

            // 👇 رجوع الرد الخام بدون أي تعديل
            $responses[] = [
                'invoice_id'   => $invoice->id,
                'http_status'  => $response->status(),
                'headers'      => $response->headers(),
                'raw_body'     => $response->body(),
                'json_body'    => $response->json(),
                'payload_sent' => $payload,
            ];
        }

        return response()->json([
            'status'    => true,
            'responses' => $responses
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'status'  => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}


}
