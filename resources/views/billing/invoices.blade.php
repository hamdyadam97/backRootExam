@extends('layouts.master')

@section('title')
    @lang('Invoices')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">@lang('Invoices')</h4>
                    <button id="sendInvoices" class="btn btn-primary waves-effect waves-light">
                        @lang('Send to Accounting')
                    </button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filter_package" class="form-label">Package</label>
                        <select id="filter_package" class="form-control select2">
                            <option value="">All Packages</option>
                            @foreach (\App\Models\Packges::orderBy('status','desc')->get() as $package)
                                <option value="{{ $package->id }}">
                                    #{{ $package->id }} - {{ $package->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive" data-simplebar>
                    <table id="invoiceTable" class="table table-hover table-bordered table-nowrap w-100 dataTable">
                        <thead class="table-light">
                            <tr>
                                <th></th>
                                <th>Invoice #</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal if needed for details -->
<div id="invoice-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Invoice Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- محتوى الفاتورة هنا -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>

<script>
    $(function () {
        $('#filter_package').select2();

        let table = $('#invoiceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('billing.list') }}",
                data: function(d){
                    d.package_id = $('#filter_package').val();
                }
            },
            columns: [
                {data: 'checkbox', orderable:false, searchable:false},
                {data: 'invoice_number'},
                {data: 'username'},
                {data: 'package'},
                {data: 'total_amount'},
                {data: 'status'},
            ]
        });

        $('#sendInvoices').click(function () {
            let ids = [];
            $('.invoice-checkbox:checked').each(function () {
                ids.push($(this).val());
            });

            if (!ids.length) {
                Swal.fire('Select invoices first');
                return;
            }

            $.post("{{ route('billing.send') }}", {
                _token: "{{ csrf_token() }}",
                invoice_ids: ids
            }, function () {
                table.ajax.reload(null, false);
            });
        });

        $('#filter_package').on('change', function() {
            table.ajax.reload(null, false);
        });
    });
</script>
@endsection
