@extends('layouts.master')
@section('title')
    @lang('Topics')
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <style type="text/css">
        .sp-original-input-container .sp-add-on {
            width: 40px !important;
        }

        option.hidden {
            display: none !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All Topics')</h4>
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
                        @lang('topics')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('topics.addupdate') }}">
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

{{--                            @if (isset($sub_categories) && !empty($sub_categories))--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="sub_category_id" class="form-label">Sub Category</label>--}}
{{--                                    <select name="sub_category_id" id="sub_category_id" class="form-control select2  sub_category_id"--}}
{{--                                        placeholder="@lang('Select sub category')">--}}
{{--                                        <option value="">Select sub category</option>--}}
{{--                                        @foreach ($sub_categories as $subcategory)--}}
{{--                                            <option class="hidden" disabled value="{{ $subcategory->id }}" data-parent="{{ $subcategory->cat_id }}">{{ $subcategory->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    <span class="invalid-feedback" id="sub_category_idError" data-ajax-feedback="sub_category_id"--}}
{{--                                        role="alert"></span>--}}
{{--                                </div>--}}
{{--                            @endif--}}


                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="topic" class="form-label">Topic</label>
                                    <input type="text" class="form-control topic" value="{{ old('topic') }}"
                                        name="topic" id="topic">
                                    <span class="invalid-feedback" id="topicError"
                                        data-ajax-feedback="topic"role="alert"></span>
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
        var apiUrl = "{{ route('topics.data') }}";
        var detailUrl = "{{ route('topics.detail') }}";
        var deleteUrl = "{{ route('topics.delete') }}";
        var addUrl = $('#add-form').attr('action');
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('topics.js') }}"></script>
@endsection
