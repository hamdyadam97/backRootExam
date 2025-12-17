@extends('layouts.master')
@section('title')
@lang('User')
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
                    <h4 class="card-title">@lang('Notification')</h4>
                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                        class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                </div>
                <div class="table-responsive" data-simplebar>
                    <table id="notificationtable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('Title')</th>
                                <th>@lang('Description')</th>
                                {{-- <th>@lang('Actions')</th> --}}
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
                <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span> @lang('Notification')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form" method="post" class="form-horizontal" action="{{ route('notification.addupdate') }}">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id" value="0" id="edit-id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input id="title" type="text" class="form-control title" name="title" value="{{ old('title') }}" >
                                <span class="invalid-feedback" id="titleError" data-ajax-feedback="title" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" type="text" class="form-control description" name="description"></textarea>
                                <span class="invalid-feedback" id="descriptionError" data-ajax-feedback="description" role="alert"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                            aria-label="Close">@lang('translation.Close')</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
                    </div>
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
    var apiUrl = "{{ route('notification.list') }}";
    var detailUrl = "{{ route('notification.detail') }}";
    var deleteUrl = "{{ route('notification.delete') }}";
    var addUrl = $('#add-form').attr('action');
    // var imgpath="{{asset('storage/notification_image')}}/";

</script>
@endsection
@section('script-bottom')
<script src="{{ addPageJsLink('notification.js') }}"></script>
@endsection