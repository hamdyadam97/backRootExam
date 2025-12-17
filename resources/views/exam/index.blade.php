@extends('layouts.master')
@section('title')
@lang('Exam')
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

 <!-- Timepicker Css -->
    <link href="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">

@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">@lang('All Exam')</h4>
                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                        class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                </div>
                <div class="table-responsive" data-simplebar>
                    <table id="exam" class="table align-middle table-hover table-nowrap w-100 dataTable">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('Category')</th>
                                <th>@lang('Subcategory')</th>
                                <th>@lang('Sub-Subcategory')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Icon')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Score')</th>
                                <th>Order</th>
                                <th>Hint</th>
                                <th>Show Hint</th>
                                <th>Show Answer</th>
                                <th>Video Link</th>
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
                <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">Add</span> Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form" method="post" class="form-horizontal" action="{{ route('exam.addupdate') }}">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id" value="0" id="edit-id">
                    <div class="row">
                        <div class="col-md-6">
                            @if (isset($categories) && !empty($categories))
                                <div class="mb-3">
                                    <label for="Category" class="form-label">Category</label>
                                    <select name="cat_id" id="cat_id"
                                        class="form-control select2  cat_id" placeholder="@lang('Select category')">
                                        <option value="">Select category</option>
                                        @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->id }}">{{ $categorie->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="cat_idError" data-ajax-feedback="cat_id"
                                    role="alert"></span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Category" class="form-label">Subcategory</label>
                                <select name="sub_cat_id" id="sub_cat_id"
                                    class="form-control select2  sub_cat_id" placeholder="@lang('Select subcategory')">
                                    <option value="">Select subcategory</option>
                                </select>
                                <span class="invalid-feedback" id="sub_cat_idError" data-ajax-feedback="sub_cat_id"
                                role="alert"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Category" class="form-label">Sub-Subcategory</label>
                                <select name="sub_sub_cat_id" id="sub_sub_cat_id" class="form-control select2  sub_sub_cat_id" placeholder="@lang('Select sub-subcategory')">
                                    <option value="">Select subcategory</option>
                                </select>
                                <span class="invalid-feedback" id="sub_sub_cat_idError" data-ajax-feedback="sub_sub_cat_id"
                                role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input id="title" type="text" class="form-control title" name="title" value="{{ old('title') }}">
                                <span class="invalid-feedback" id="titleError" data-ajax-feedback="title" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time" class="form-label">Time</label>
                                <div class="input-group" id="timepicker-input-time">
                                    <input id="time" type="text" class="form-control time" name="time" value="0:00">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                    <span class="invalid-feedback" id="timeError" data-ajax-feedback="time" role="alert"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="score" class="form-label">Score</label>
                                <input id="score" type="text" class="form-control score" name="score" value="{{ old('score') }}">
                                <span class="invalid-feedback" id="scoreError" data-ajax-feedback="score" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select general-select type">
                                    <option value="">Select Type</option>
                                    @foreach(\App\Models\Exams::$exam_type as $k=>$l)
                                    <option value="{{$k}}">{{$l}}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="typeError" data-ajax-feedback="type"
                                role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order" class="form-label">Order</label>
                                <input id="order" type="text" class="form-control order" name="order" value="{{ old('order') }}">
                                <span class="invalid-feedback" id="orderError" data-ajax-feedback="order" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hint" class="form-label">Hint</label>
                                <textarea  class="form-control" id="hint" name="hint" rows="3"></textarea>
                                <span class="invalid-feedback" id="hintError" data-ajax-feedback="hint" role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="show_hint" class="form-label">Show Hint</label>
                                <select name="show_hint" id="show_hint" class="form-select general-select show_hint">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                <span class="invalid-feedback" id="show_hintError" data-ajax-feedback="show_hint" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="show_answer" class="form-label">Show Answer</label>
                                 <select name="show_answer" id="show_answer" class="form-select general-select show_answer">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                <span class="invalid-feedback" id="show_answerError" data-ajax-feedback="show_answer" role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="video_link" class="form-label">Video Link</label>
                                <input id="video_link" type="text" class="form-control video_link" name="video_link" value="{{ old('video_link') }}">
                                <span class="invalid-feedback" id="video_linkError" data-ajax-feedback="video_link"
                                role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status_type" class="form-select general-select status">
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                                <span class="invalid-feedback" id="statusError" data-ajax-feedback="status"
                                role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                             <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea  class="form-control" id="description" name="description" rows="3"></textarea>
                                <span class="invalid-feedback" id="descriptionError" data-ajax-feedback="description" role="alert"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon</label>
                                <input id="icon" type="file" class="form-control icon" name="icon" title="icon">
                                <span class="invalid-feedback" id="iconError" data-ajax-feedback="icon" role="alert"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-3">
                                <div class="view-image" style="display: none;">
                                    <img src="" class="exam_img" height="50px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
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
<!-- Sweet Alerts js -->
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
 <!-- select2 js -->
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
 <!-- Timepicker js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
<script>
    var apiUrl = "{{ route('exam.list') }}";
    var detailUrl = "{{ route('exam.detail') }}";
    var deleteUrl = "{{ route('exam.delete') }}";
    var addUrl = $('#add-form').attr('action');
    var imgpath="{{asset('storage/exam_icon')}}/";
    var subcatUrl="{{route('exam.subcat')}}";
    var sub_subcatUrl="{{route('exam.sub-subcat')}}";
    var copyExam = "{{ route('exam.copy') }}";
</script>
@endsection
@section('script-bottom')
<script src="{{ addPageJsLink('exam.js') }}"></script>
@endsection
