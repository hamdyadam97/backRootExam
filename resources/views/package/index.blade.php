@extends('layouts.master')
@section('title')
    @lang('Package')
@endsection
@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All Package')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                            <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="package" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Icon')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Sub Category')</th>
{{--                                <th>@lang('Number of questions')</th>--}}
{{--                                <th>@lang('Number of Exams')</th>--}}
{{--                                <th>@lang('Number of Trials')</th>--}}
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
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span
                            class="modal-lable-class">@lang('Add')</span> @lang('Package')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('package.addupdate') }}">
                    @csrf
                    <input type="hidden" name="id" value="0" id="edit-id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control name" value="{{ old('name') }}" name="name"
                                           id="name">
                                    <span class="invalid-feedback" id="nameError" data-ajax-feedback="name"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" class="form-control price" value="{{ old('price') }}"
                                           name="price" id="price">
                                    <span class="invalid-feedback" id="priceError" data-ajax-feedback="price"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{--                        <div class="col-md-6">--}}
                            {{--                            <div class="col-md-12">--}}
                            {{--                                <div class="mb-3">--}}
                            {{--                                    <label for="Number of questions" class="form-label">Number of questions</label>--}}
                            {{--                                    <input type="text" class="form-control number_of_questions" value="{{ old('number_of_questions') }}" name="number_of_questions" id="number_of_questions">--}}
                            {{--                                    <span class="invalid-feedback" id="number_of_questionsError" data-ajax-feedback="number_of_questions"role="alert"></span>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                            {{--                        </div>--}}
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="period" class="form-label">Period</label>
                                        <input type="number" class="form-control period" value="{{ old('period') }}"
                                               name="period" id="period">
                                        <small id="passwordHelpBlock" class="form-text text-muted">
                                            Period in days
                                        </small>
                                        <span class="invalid-feedback" id="periodError" data-ajax-feedback="period"
                                              role="alert"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
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
                        {{--                    <div class="row">--}}
                        {{--                        <div class="col-md-6">--}}
                        {{--                            <div class="col-md-12">--}}
                        {{--                                <div class="mb-3">--}}
                        {{--                                    <label for="no_of_exams" class="form-label">Number of Exams</label>--}}
                        {{--                                    <input type="number" class="form-control no_of_exams" value="{{ old('no_of_exams') }}" name="no_of_exams" id="no_of_exams">--}}
                        {{--                                    <span class="invalid-feedback" id="no_of_examsError" data-ajax-feedback="no_of_exams"role="alert"></span>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        {{--                        <div class="col-md-6">--}}
                        {{--                            <div class="col-md-12">--}}
                        {{--                                <div class="mb-3">--}}
                        {{--                                    <label for="no_of_trial" class="form-label">Number of Trials</label>--}}
                        {{--                                    <input type="number" class="form-control no_of_trial" value="{{ old('no_of_trial') }}" name="no_of_trial" id="no_of_trial">--}}
                        {{--                                    <span class="invalid-feedback" id="no_of_trialError" data-ajax-feedback="no_of_trial"role="alert"></span>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        {{--                    </div>--}}
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-control select2"
                                            data-placeHolder="@lang('Select Category')">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="text_question"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Sub Category</label>
                                    <select name="sub_category_id[]" multiple id="sub_category_id"
                                            class="form-control select2"
                                            data-placeHolder="@lang('Select Sub Category')">

                                    </select>
                                    <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="text_question"
                                          role="alert"></span>
                                </div>
                            </div>


                            {{--                        <div class="col-md-6">--}}
                            {{--                            <div class="mb-3">--}}
                            {{--                                <label for="Text" class="form-label">Exam</label>--}}
                            {{--                                <select name="exam_id[]" id="exam_id" class="form-control select2 exam_id" multiple data-placeHolder="@lang('Select Exam')">--}}
                            {{--                                    @foreach ($exams as $exam)--}}
                            {{--                                        <option value="{{ $exam->id }}">{{ $exam->title }}</option>--}}
                            {{--                                    @endforeach--}}
                            {{--                                </select>--}}
                            {{--                                <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="text_question"role="alert"></span>--}}
                            {{--                            </div>--}}
                            {{--                        </div>--}}

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <input id="icon" type="file" class="form-control icon" name="icon" title="icon">
                                    <span class="invalid-feedback" id="iconError" data-ajax-feedback="icon"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <div class="view-image" style="display: none;">
                                        <img src="" class="pack_img" height="50px;">
                                    </div>
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
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Inputmask js -->
    {{-- <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script> --}}
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('package.list') }}";
        var detailUrl = "{{ route('package.detail') }}";
        var deleteUrl = "{{ route('package.delete') }}";
        var addUrl = $('#add-form').attr('action');
        var imgpath = "{{asset('storage/Package_icon')}}/";

        var categories = @json($categories)
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('package.js') }}"></script>
@endsection
