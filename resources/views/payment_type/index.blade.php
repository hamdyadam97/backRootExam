@extends('layouts.master')
@section('title')
@lang('Package')
@endsection
@section('css')
<!-- Datatable Css -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
    type="text/css" />
<!-- Datepicker Css -->
<link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
    type="text/css">
<!-- Sweet Alert-->
<link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">@lang('All Payment Types')</h4>
                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                        <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                </div>
                <div class="table-responsive" data-simplebar>
                    <table id="payment-types" class="table align-middle table-hover table-nowrap w-100 dataTable">
                        <thead class="table-light">
                            <tr>
                                <th class="d-none">@lang('id')</th>
                                <th>@lang('Payment Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<!-- Page Models -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('Add')</span> @lang('Payment Type')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form" method="post" class="form-horizontal" action="{{ route('payment-types.addupdate') }}">
                @csrf
                <input type="hidden" name="id" value="0" id="edit-id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_type" class="form-label">Payment Type</label>
                                <input type="text" class="form-control payment_type" value="{{ old('payment_type') }}" name="payment_type" id="payment_type">
                                <span class="invalid-feedback" id="payment_typeError" data-ajax-feedback="payment_type"role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status_type" class="form-control status">
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                                <span class="invalid-feedback" id="statusError" data-ajax-feedback="status"
                                role="alert"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal"
                        aria-label="Close">@lang('translation.Close')</button>
                    <button type="submit" class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- Datatable js -->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<!-- Datepicker Css -->
<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<!-- Inputmask js -->
{{-- <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script> --}}
<!-- Sweet Alerts js -->
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<script>
    var apiUrl = "{{ route('payment-types.list') }}";
    var addUrl = $('#add-form').attr('action');
    var deleteUrl = "{{ route('payment-types.delete') }}";
    var detailUrl = "{{ route('payment-types.detail') }}";
</script>
@endsection
@section('script-bottom')
<script src="{{ addPageJsLink('paymenttype.js') }}"></script>
@endsection
