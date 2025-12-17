@extends('layouts.master')
@section('title')
@lang('Exam question')
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

@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">@lang('All Exam question')</h4>
                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                        <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                </div>
                <div class="table-responsive" data-simplebar>
                    <table id="subcategory" class="table align-middle table-hover table-nowrap w-100 dataTable">
                        <thead class="table-light">
                            <tr>
                                <th>Exam</th>
                                <th>Question</th>
                                <th>Actions</th>
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
                <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('Add')</span> @lang('Exam question')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-form" method="post" class="form-horizontal" action="{{ route('examquestion.addupdate') }}">
                @csrf
                <input type="hidden" name="id" value="0" id="edit-id">
                <div class="modal-body">
                    <div class="row">
                        @if (isset($exams) && !empty($exams))
                            <div class="mb-3">
                                <label for="Exam" class="form-label">Exam</label>
                                <select name="exam_id" id="exam_id"
                                    class="form-control select2  exam_id" placeholder="@lang('Select exam')">
                                    <option value="">Select exam</option>
                                    @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="exam_id"
                                role="alert"></span>
                            </div>
                        @endif
                        @if (isset($questions) && !empty($questions))
                            <div class="mb-3">
                                <label for="Question" class="form-label">Question</label>
                                <select name="question_id" id="question_id"
                                    class="form-control select2  question_id" placeholder="@lang('Select Question')">
                                    <option value="">Select Question</option>
                                    @foreach ($questions as $question)
                                    <option value="{{ $question->id }}">{{ $question->text_question }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="question_idError" data-ajax-feedback="question_id"
                                role="alert"></span>
                            </div>
                        @endif
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
<!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<script>
    var apiUrl = "{{ route('examquestion.list') }}";
    var detailUrl = "{{ route('examquestion.detail') }}";
    var deleteUrl = "{{ route('examquestion.delete') }}";
    var addUrl = $('#add-form').attr('action');
</script>
@endsection
@section('script-bottom')
<script src="{{ addPageJsLink('examquestion.js') }}"></script>
@endsection