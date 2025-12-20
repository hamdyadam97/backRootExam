@extends('layouts.master')
@section('title')
    @lang('Invoices')
@endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">@lang('All Invoices')</h4>
                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                        <i class="bx bx-plus label-icon"></i> @lang('Add Invoice')
                    </button>
                </div>
                <button class="btn btn-success" id="sendInvoices">
    <i class="bx bx-send"></i> Send to Invoicing System
</button>


                <div class="table-responsive" data-simplebar>
                    <table id="invoiceTable" class="table align-middle table-hover table-nowrap w-100">
                        <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                            </th>

                            <th>ID</th>
                            <th>Invoice #</th>
                            <th>User</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div id="add-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="modal-lable-class">Add</span> Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="add-form" method="post" action="{{ url('billing/addupdate') }}">
                @csrf
                <input type="hidden" name="id" id="edit-id" value="0">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control">
                        <span class="invalid-feedback" id="total_amountError"></span>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Invoice Number</label>
                         <input type="text" id="invoice_number" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">User</label>
                         <input type="text" id="username" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Package</label>
                         <input type="text" id="package_name" class="form-control" readonly>
                    </div>
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>

<script>
    var apiUrl   = "{{ route('billing.list') }}";
    var detailUrl = "{{ url('billing/detail') }}";
    var deleteUrl = "{{ url('billing/delete') }}";
    var addUrl   = $('#add-form').attr('action');
    var exportUrl = "{{ route('billing.sendToSystem') }}";


</script>
@endsection

@section('script-bottom')
<script src="{{ addPageJsLink('billing.js') }}"></script>
@endsection
