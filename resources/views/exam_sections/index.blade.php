@extends('layouts.master')
@section('title')
    @lang('Exam Section')
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

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('assets/libs/spectrum-colorpicker2/spectrum.min.css') }}" rel="stylesheet" type="text/css">
    <style type="text/css">
        .sp-original-input-container .sp-add-on {
            width: 40px !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All Exam Section')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                            <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="Text" class="form-label">Category</label>
                                <select required name="category_id"
                                        class="form-control filter_category category_id" placeholder="@lang('Select Category')">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="category_idError" data-ajax-feedback="category_id"
                                      role="alert"></span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" data-simplebar>
                        <table id="subcategory" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
{{--                                    <th>@lang('Sub Category')</th>--}}
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
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('Add')</span>
                        @lang('Exam Section')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('exam_section.addupdate') }}">
                    @csrf
                    <input type="hidden" name="id" value="0" id="edit-id">
                    <div class="modal-body">
                        <div class="row">
                            @if (isset($categories) && !empty($categories))
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-control select2  category_id"
                                        placeholder="@lang('Select category')">
                                        <option value="">Select category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="category_idError" data-ajax-feedback="category_id"
                                        role="alert"></span>
                                </div>
                            @endif

                            @if (isset($sub_categories) && !empty($sub_categories))
                                <div class="mb-3">
                                    <label for="sub_category_id" class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id"
                                        class="form-control select2  sub_category_id" placeholder="@lang('Select sub category')">
                                        <option value="">Select sub category</option>
                                        @foreach ($sub_categories as $subcategory)
                                            <option class="hidden" disabled value="{{ $subcategory->id }}"
                                                data-parent="{{ $subcategory->cat_id }}">{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="sub_category_idError"
                                        data-ajax-feedback="sub_category_id" role="alert"></span>
                                </div>
                            @endif


                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control name" value="{{ old('name') }}"
                                        name="name" id="name">
                                    <span class="invalid-feedback" id="nameError"
                                        data-ajax-feedback="name"role="alert"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal"
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
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>


    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('exam_section.data') }}";

        var detailUrl = "{{ route('exam_section.detail') }}";
        var deleteUrl = "{{ route('exam_section.delete') }}";
        var addUrl = $('#add-form').attr('action');
    </script>
    <script>
        $('.select2').select2()

        $('.filter_category').select2({
            placeholder: $(this).attr('placeholder'),
            allowClear: true,
            dropdownParent: $('#filter')
        });
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('exam_section.js') }}"></script>

@endsection
