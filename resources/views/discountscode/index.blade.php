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
                        <h4 class="card-title">@lang('All Discounts Code')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                            <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="discountscode" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                            <tr>
                                <th class="d-none">@lang('id')</th>
                                <th>@lang('Code')</th>
                                <th>@lang('Marketer')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Percentage / Amount')</th>
                                <th>@lang('Quantity')</th>
                                <th>@lang('Usage')</th>
                                <th>@lang('From Date')</th>
                                <th>@lang('To Date')</th>
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
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('Add')</span> @lang('Discounts Code')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('discountscode.addupdate') }}">
                    @csrf
                    <input type="hidden" name="id" value="0" id="edit-id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control code" value="{{ old('code') }}" name="code" id="code">
                                    <span class="invalid-feedback" id="codeError" data-ajax-feedback="code"role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marketer" class="form-label">Marketer</label>
                                    <input type="text" class="form-control marketer" value="{{ old('marketer') }}" name="marketer" id="marketer">
                                    <span class="invalid-feedback" id="marketerError" data-ajax-feedback="marketer"role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-control type">
                                        <option value="1">Percentage</option>
                                        <option value="2">Amount</option>
                                    </select>
                                    <span class="invalid-feedback" id="typeError" data-ajax-feedback="type" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="percentage-section">
                                <div class="mb-3">
                                    <label for="percentage" class="form-label">Percentage</label>
                                    <input type="number" class="form-control percentage" min="0" step="0.5" value="{{ old('percentage') }}" name="percentage" id="percentage">
                                    <span class="invalid-feedback" id="percentageError" data-ajax-feedback="percentage" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6 d-none" id="amount-section">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control amount" min="0" value="{{ old('amount') }}" name="amount" id="amount">
                                    <span class="invalid-feedback" id="amountError" data-ajax-feedback="amount" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control quantity" min="0" value="{{ old('quantity') }}" name="quantity" id="quantity">
                                    <span class="invalid-feedback" id="quantityError" data-ajax-feedback="quantity" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_date" class="control-label">From date</label>
                                    <div class="input-group" id="start_date_container">
                                        <input type="text" id="from_date" name="from_date" class="form-control"
                                               value="{{ old('from_date') }}"
                                               data-date-format="dd.mm.yyyy" data-date-container='#start_date_container'
                                               data-provide="datepicker" data-date-autoclose="true">
                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                        <span class="invalid-feedback" id="from_dateError" data-ajax-feedback="from_date" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="to_date" class="control-label">To date</label>
                                    <div class="input-group" id="end_date_container">
                                        <input type="text" id="to_date" name="to_date" class="form-control"
                                               value="{{ old('to_date') }}"
                                               data-date-format="dd.mm.yyyy" data-date-container='#end_date_container'
                                               data-provide="datepicker" data-date-autoclose="true">
                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                        <span class="invalid-feedback" id="to_dateError"
                                              data-ajax-feedback="to_date" role="alert"></span>
                                    </div>
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
        var apiUrl = "{{ route('discountscode.list') }}";
        var addUrl = $('#add-form').attr('action');
        var deleteUrl = "{{ route('discountscode.delete') }}";
        var detailUrl = "{{ route('discountscode.detail') }}";
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('discountscode.js') }}?v={{\Illuminate\Support\Str::random(3)}}"></script>
@endsection
