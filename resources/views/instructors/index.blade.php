@extends('layouts.master')
@section('title')
    @lang('Instructors')
@endsection
@push('admin_css')
    <!-- Datatable Css -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <!-- Datepicker Css -->
    <link href="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All Instructor')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="usertable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('specialization')</th>
                                <th>@lang('Image')</th>
                                <th>@lang('rate')</th>
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
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span
                            class="modal-lable-class">@lang('translation.Add')</span> @lang('User')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('instructors.addupdate') }}">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input id="name" type="text" class="form-control name" name="name"
                                           value="{{ old('name') }}">
                                    <span class="invalid-feedback" id="nameError" data-ajax-feedback="name"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="specialization" class="form-label">Specialization</label>
                                    <input id="specialization" type="text" class="form-control specialization" name="specialization"
                                           value="{{ old('specialization') }}">
                                    <span class="invalid-feedback" id="specializationError" data-ajax-feedback="specialization"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rate" class="form-label">Rate</label>
                                    <input id="rate" type="number" class="form-control rate" name="rate"
                                           value="{{ old('score') }}" min="0" max="5" step="0.1">
                                    <span class="invalid-feedback" id="rateError" data-ajax-feedback="rate"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    <input id="image" type="file" class="form-control image" name="image" title="image">
                                    <span class="invalid-feedback" id="imageError" data-ajax-feedback="image"
                                          role="alert"></span>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mt-3">
                                    <div class="view-image" style="display: none;">
                                        <img src="" class="user_img" height="50px;">
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                        <button type="submit"
                                class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- Datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('instructors.list') }}";
        var detailUrl = "{{ route('instructors.detail') }}";
        var deleteUrl = "{{ route('instructors.delete') }}";
        var addUrl = $('#add-form').attr('action');
        var imgpath = "{{asset('storage/instructor_image')}}/";

    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('instructors.js') }}"></script>
@endsection
