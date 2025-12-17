<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        return view('billing.invoices');
    }

    public function get()
    {
        $data = Invoice::with(['user', 'userPackage.getPackage'])
            ->orderByDesc('id');

        return datatables($data)
            ->addColumn('checkbox', function ($row) {
                if ($row->sent_to_accounting) return '';
                return '<input type="checkbox" class="invoice-checkbox" value="'.$row->id.'">';
            })
            ->addColumn('username', fn($row) => $row->user->first_name.' '.$row->user->last_name)
            ->addColumn('package', fn($row) => $row->userPackage->getPackage->name ?? '')
            ->editColumn('status', fn($row) => ucfirst($row->status))
            ->escapeColumns([])
            ->make(true);
            
    }

    public function send(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            $invoices = Invoice::whereIn('id', $request->invoice_ids)
                ->where('sent_to_accounting', 0)
                ->get();

            foreach ($invoices as $invoice) {
                // 🔗 ربط نظام المحاسبة هنا
                // sendInvoiceToAccounting($invoice);

                $invoice->update([
                    'sent_to_accounting' => 1,
                    'status' => 'sent',
                    'sent_at' => now()
                ]);
            }

            DB::commit();
            return response()->json(['status' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
