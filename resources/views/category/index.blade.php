@extends('layouts.master')
@section('title')
@lang('Category')
@endsection
@push('admin_css')
<!-- Datatable Css -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
    type="text/css" />
<!-- Datepicker Css -->
<link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
    type="text/css">
<!-- Sweet Alert-->
<link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/spectrum-colorpicker2/spectrum.min.css') }}" rel="stylesheet" type="text/css">
<style type="text/css">
    .sp-original-input-container .sp-add-on{
        width: 40px !important;
    }
    .form-switch .form-check-input{
        height: 2em !important;
        width: 4em !important;
    }
</style>
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">@lang('All Category')</h4>
                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                        <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                </div>
                <div class="table-responsive" data-simplebar>
                    <table id="category" class="table align-middle table-hover table-nowrap w-100 dataTable">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Icon')</th>
                                <th>@lang('Order')</th>
                                <th>Foreground Color</th>
                                <th>Background Color</th>
                                <th>@lang('Is Top?')</th>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('Add')</span> @lang('Category')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form" method="post" class="form-horizontal" action="{{ route('category.addupdate') }}">
                @csrf
                <input type="hidden" name="id" value="0" id="edit-id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control name" value="{{ old('name') }}" name="name" id="name">
                                <span class="invalid-feedback" id="nameError" data-ajax-feedback="name"role="alert"></span>
                            </div>
                        </div>
                        <div class="view-image" style="display: none;">
                            <img src="" class="cat_img" height="50px;">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="icon" class="form-label">Icon</label>
                            <input id="icon" type="file" class="form-control icon" name="icon" title="icon">
                            <span class="invalid-feedback" id="iconError" data-ajax-feedback="icon" role="alert"></span>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="mb-3">
                                <label for="order" class="form-label">Order</label>
                                <input type="text" class="form-control order" value="{{ old('order') }}" name="order" id="order">
                                <span class="invalid-feedback" id="orderError" data-ajax-feedback="order"role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="mb-3">
                                <label for="foreground_color" class="form-label">Foreground Color</label>
                                <input type="text" class="form-control" name="foreground_color" id="foreground_color" value="#000000">
                                <span class="invalid-feedback d-block" id="foreground_colorError" data-ajax-feedback="order"role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="mb-3">
                                <label for="background_color" class="form-label">Background Color</label>
                                <input type="text" class="form-control" name="background_color" id="background_color" value="#000000">
                                <span class="invalid-feedback d-block" id="background_colorError" data-ajax-feedback="order"role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status_type" class="form-select status">
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
<script src="{{ URL::asset('assets/libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
<script>
    var apiUrl = "{{ route('category.list') }}";
    var detailUrl = "{{ route('category.detail') }}";
    var deleteUrl = "{{ route('category.delete') }}";
    var addUrl = $('#add-form').attr('action');
    var imgpath="{{asset('storage/category_icon')}}/";
</script>
@endsection
@section('script-bottom')
<script src="{{ addPageJsLink('category.js') }}"></script>
@endsection
