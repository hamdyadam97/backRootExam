@extends('layouts.master')
@section('title')
    @lang('Questions')
@endsection
@push('admin_css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">

    <script>
        window.MathJax = {
            MathML: {
                extensions: ["mml3.js", "content-mathml.js"]
            }
        };
    </script>
    <script type="text/javascript" async
            src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=MML_HTMLorMML"></script>
@endpush
@section('content')
    <div class="row">

        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All Questions')</h4>
                        {{-- Filter --}}
                        {{--                        <div class="col-md-6 mt-3"> --}}
                        {{--                            <div class="mb-3"> --}}
                        {{--                                --}}{{-- <label for="Text" class="form-label">Exam</label> --}}
                        {{--                                <select name="exam_filter" id="exam_filter" class="form-control select2 exam_id" placeholder="@lang('Select Exam')"> --}}
                        {{--                                    <option value="">Select Exam</option> --}}
                        {{--                                    @foreach ($exams as $exam) --}}
                        {{--                                        <option value="{{ $exam->id }}">{{ $exam->title }}</option> --}}
                        {{--                                    @endforeach --}}
                        {{--                                </select> --}}
                        {{--                                <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="text_question"role="alert"></span> --}}
                        {{--                            </div> --}}
                        {{--                        </div> --}}

                        <div>
                            <a href="{{ route('question.import') }}"
                               class="btn btn-primary waves-effect btn-label waves-light"><i
                                    class='bx bx-import label-icon'></i> @lang('Import')</a>
                            <a href="{{ asset('templates/questions.csv') }}"
                               class="btn btn-primary waves-effect btn-label waves-light"><i
                                    class='bx bx-import label-icon'></i> @lang('Download Template')</a>
                            <a href="{{ route('question.sorting') }}"
                               class="btn btn-primary  waves-light">@lang('Sorting')</a>

                            {{-- End filter --}}
                            <a href="{{ route('question.create') }}"
                               class="btn btn-primary waves-effect btn-label waves-light">
                                <i class="bx bx-plus label-icon"></i>@lang('Add New')</a>
                        </div>
                    </div>
                    <form action="{{ route('question.export') }}" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Category</label>
                                    <select name="category_id" id="category_id"
                                            class="form-control select2 category_id"
                                            placeholder="@lang('Select Category')">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option
                                                value="{{ $category->id }}" {{ $category->id == $session_category_id ? 'selected':'' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="category_idError"
                                          data-ajax-feedback="category_id"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id"
                                            class="form-control select2 category_id"
                                            placeholder="@lang('Select Sub Category')">
                                        <option value="">Select SubCategory</option>

                                        @isset($sub_categories)
                                            @foreach($sub_categories as $sub_category)
                                                <option
                                                    value="{{ $sub_category->id }}" {{ $sub_category->id == $session_sub_category_id ? 'selected':'' }}>{{ $sub_category->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <span class="invalid-feedback" id="sub_category_idError"
                                          data-ajax-feedback="sub_category_id" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Sub SubCategory</label>
                                    <select name="sub_subcategory_id" id="sub_subcategory_id"
                                            class="form-control select2 category_id"
                                            placeholder="@lang('Select Sub SubCategory')">
                                        <option value="">Select Sub SubCategory</option>

                                        @isset($sub_sub_categories)
                                            @foreach($sub_sub_categories as $sub_sub_category)
                                                <option
                                                    value="{{ $sub_sub_category->id }}" {{ $sub_sub_category->id == $session_sub_subcategory_id ? 'selected':'' }}>{{ $sub_sub_category->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <span class="invalid-feedback" id="sub_subcategory_idError"
                                          data-ajax-feedback="sub_subcategory_id" role="alert"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Topic</label>
                                    <select name="topic_id" id="topic_id" class="form-control select2 topic_id"
                                            placeholder="@lang('Select Topic')">
                                        <option value="">Select Topic</option>

                                        @isset($topics)
                                            @foreach($topics as $topic)
                                                <option
                                                    value="{{ $topic->id }}" {{ $topic->id == $session_questions_topic_id ? 'selected':'' }}>{{ $topic->topic }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <span class="invalid-feedback" id="topic_idError" data-ajax-feedback="topic_id"
                                          role="alert"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Exam Section</label>
                                    <select name="exam_section_id" id="exam_section_id"
                                            class="form-control select2 exam_section_id"
                                            placeholder="@lang('Select Exam Section')">
                                        <option value="">Select Exam section</option>

                                        @isset($sections)
                                            @foreach($sections as $section)
                                                <option
                                                    value="{{ $section->id }}" {{ $section->id == $session_section_id ? 'selected':'' }}>{{ $section->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <span class="invalid-feedback" id="exam_section_idError"
                                          data-ajax-feedback="exam_section_id" role="alert"></span>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="page" class="form-label">Page No.</label>
                                    <input class="form-control" name="page" id="page">
                                    <span class="invalid-feedback" id="pageError"
                                          data-ajax-feedback="page" role="alert"></span>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mt-4">
                                    <button type="button" id="filter" class="btn btn-primary waves-light">@lang('Filter')</button>
                                    <button class="btn btn-primary  waves-light">@lang('Export')</button>

                                </div>
                            </div>

                        </div>
                    </form>
                    <div class="table-responsive" data-simplebar>
                        <table id="question"
                               class="table align-middle table-hover table-nowrap w-100 dataTable text-center">
                            <thead class="table-light">
                            <tr>
                                <th style="max-width: 5%!important;" class="text-center">Id</th>
                                <th style="max-width: 30%!important;" class="text-center">@lang('Text')</th>
                                <th style="max-width: 5%!important;" class="text-center">@lang('Type')</th>
                                <th style="max-width: 10%!important;" class="text-center">@lang('Hint')</th>
                                <th style="max-width: 30%!important;" class="text-center">@lang('Correct answer')</th>
                                {{--                                                                    <th>@lang('Type')</th>--}}
                                {{--                                <th>@lang('Answer type')</th> --}}
                                {{--                                <th>@lang('Correct answer')</th> --}}
                                {{--                                <th>Hint</th> --}}
                                {{--                                <th>Show Hint</th> --}}
                                {{--                                <th>Show Answer</th> --}}
                                {{--                                <th>Video Link</th> --}}
                                <th style="max-width: 10%!important;" class="text-center">@lang('Status')</th>
                                <th style="max-width: 10%!important;" class="text-center">@lang('Actions')</th>
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
    {{--    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"--}}
    {{--         aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">--}}
    {{--        <div class="modal-dialog modal-lg modal-dialog-centered">--}}
    {{--            <div class="modal-content">--}}
    {{--                <div class="modal-header">--}}
    {{--                    <h5 class="modal-title" id="myLargeModalLabel"><span--}}
    {{--                            class="modal-lable-class">@lang('Add')</span> @lang('Question')</h5>--}}
    {{--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
    {{--                </div>--}}
    {{--                <form id="add-form" method="post" class="form-horizontal" action="{{ route('question.addupdate') }}">--}}
    {{--                    <div class="modal-body">--}}
    {{--                        @csrf--}}
    {{--                        <input type="hidden" name="id" value="" id="edit-id">--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="Text" class="form-label">Exam</label>--}}
    {{--                                    <select name="exam_id" id="exam_id" class="form-control select2 exam_id"--}}
    {{--                                            placeholder="@lang('Select Exam')">--}}
    {{--                                        <option value="">Select Exam</option>--}}
    {{--                                        @foreach ($exams as $exam)--}}
    {{--                                            <option value="{{ $exam->id }}">{{ $exam->title }}</option>--}}
    {{--                                        @endforeach--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="exam_idError"--}}
    {{--                                          data-ajax-feedback="text_question" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="Text" class="form-label">Exam Section</label>--}}
    {{--                                    <select name="section_id" id="section_id" class="form-control select2 exam_id"--}}
    {{--                                            placeholder="@lang('Select Exam Section')">--}}
    {{--                                        <option value="">Select Exam Section</option>--}}
    {{--                                        @foreach ($sections as $section)--}}
    {{--                                            <option value="{{ $section->id }}">{{ $section->name }}</option>--}}
    {{--                                        @endforeach--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="exam_idError"--}}
    {{--                                          data-ajax-feedback="text_question" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-12">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="Text" class="form-label">Text</label>--}}
    {{--                                    <input type="text" class="form-control text_question"--}}
    {{--                                           value="{{ old('text_question') }}" name="text_question" id="text_question">--}}
    {{--                                    <span class="invalid-feedback" id="text_questionError"--}}
    {{--                                          data-ajax-feedback="text_question" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-12">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="notes" class="form-label">Notes</label>--}}
    {{--                                    <textarea id="editor" class="form-control" name="notes" rows="3">--}}
    {{--                                </textarea>--}}
    {{--                                    <span class="invalid-feedback" id="notesError" data-ajax-feedback="notes"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="question_type" class="form-label">Question type</label>--}}
    {{--                                    <select name="question_type" id="question_type" class="form-select question_type">--}}
    {{--                                        <option value="">Select Question type</option>--}}
    {{--                                        @if ($questionType)--}}
    {{--                                            @foreach ($questionType as $key1 => $row1)--}}
    {{--                                                <option value="{{ $key1 }}">{{ $row1 }}</option>--}}
    {{--                                            @endforeach--}}
    {{--                                        @endif--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="question_typeError"--}}
    {{--                                          data-ajax-feedback="question_type" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="answer_type" class="form-label">Answer type</label>--}}
    {{--                                    <select name="answer_type" id="answer_type" class="form-select answer_type">--}}
    {{--                                        <option value="">Select Answer type</option>--}}
    {{--                                        <option value="1">Radio</option>--}}
    {{--                                        <option value="2">Multiple choice</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="answer_typeError"--}}
    {{--                                          data-ajax-feedback="answer_type"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="hint" class="form-label">Hint</label>--}}
    {{--                                    <textarea class="form-control" id="hint" name="hint" rows="3"></textarea>--}}
    {{--                                    <span class="invalid-feedback" id="hintError" data-ajax-feedback="hint"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="show_hint" class="form-label">Show Hint</label>--}}
    {{--                                    <select name="show_hint" id="show_hint"--}}
    {{--                                            class="form-select general-select show_hint">--}}
    {{--                                        <option value="0">No</option>--}}
    {{--                                        <option value="1">Yes</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="show_hintError" data-ajax-feedback="show_hint"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="show_answer" class="form-label">Show Answer</label>--}}
    {{--                                    <select name="show_answer" id="show_answer"--}}
    {{--                                            class="form-select general-select show_answer">--}}
    {{--                                        <option value="0">No</option>--}}
    {{--                                        <option value="1">Yes</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="show_answerError"--}}
    {{--                                          data-ajax-feedback="show_answer"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="video_link" class="form-label">Video Link</label>--}}
    {{--                                    <input id="video_link" type="text" class="form-control video_link"--}}
    {{--                                           name="video_link" value="{{ old('video_link') }}">--}}
    {{--                                    <span class="invalid-feedback" id="video_linkError" data-ajax-feedback="video_link"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="time_minutes" class="form-label">Time in Minutes</label>--}}
    {{--                                    --}}{{-- <textarea  class="form-control" id="time_minutes" name="time_minutes" rows="3"></textarea> --}}
    {{--                                    <input id="time_minutes" type="number" class="form-control time_minutes"--}}
    {{--                                           name="time_minutes" value="{{ old('time_minutes') }}">--}}
    {{--                                    <span class="invalid-feedback" id="time_minutesError"--}}
    {{--                                          data-ajax-feedback="time_minutes" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="show_video" class="form-label">Show Video</label>--}}
    {{--                                    <select name="show_video" id="show_video"--}}
    {{--                                            class="form-select general-select show_video">--}}
    {{--                                        <option value="0">No</option>--}}
    {{--                                        <option value="1">Yes</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="show_videoError" data-ajax-feedback="show_video"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <div class="view-question-image" style="display: none;">--}}
    {{--                                        <img src="" class="question_img" height="50px;">--}}
    {{--                                    </div>--}}
    {{--                                    <label for="hint" class="form-label">Question Image</label>--}}
    {{--                                    <input id="question_image" type="file" class="form-control question_image"--}}
    {{--                                           name="question_image" title="question_image">--}}
    {{--                                    <span class="invalid-feedback" id="question_imageError"--}}
    {{--                                          data-ajax-feedback="question_image" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="show_hint" class="form-label">Show Question Image</label>--}}
    {{--                                    <select name="question_has_image" id="question_has_image"--}}
    {{--                                            class="form-select general-select show_hint">--}}
    {{--                                        <option value="0">No</option>--}}
    {{--                                        <option value="1">Yes</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="question_has_imageError"--}}
    {{--                                          data-ajax-feedback="question_has_image" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <div class="view-answer-image" style="display: none;">--}}
    {{--                                        <img src="" class="answer_img" height="50px;">--}}
    {{--                                    </div>--}}
    {{--                                    <label for="hint" class="form-label">Answer Image</label>--}}
    {{--                                    <input id="answer_image" type="file" class="form-control answer_image"--}}
    {{--                                           name="answer_image" title="answer_image">--}}
    {{--                                    <span class="invalid-feedback" id="answer_imageError"--}}
    {{--                                          data-ajax-feedback="question_image" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="answer_has_image" class="form-label">Show Answer Image</label>--}}
    {{--                                    <select name="answer_has_image" id="answer_has_image"--}}
    {{--                                            class="form-select general-select show_hint">--}}
    {{--                                        <option value="0">No</option>--}}
    {{--                                        <option value="1">Yes</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="answer_has_imageError"--}}
    {{--                                          data-ajax-feedback="answer_has_image" role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}

    {{--                        <div class="row">--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <div class="mb-3">--}}
    {{--                                    <label for="status" class="form-label">Status</label>--}}
    {{--                                    <select name="status" id="status_type" class="form-select status">--}}
    {{--                                        <option value="1">Active</option>--}}
    {{--                                        <option value="0">Deactive</option>--}}
    {{--                                    </select>--}}
    {{--                                    <span class="invalid-feedback" id="statusError" data-ajax-feedback="status"--}}
    {{--                                          role="alert"></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="col-md-6">--}}
    {{--                                <label class="form-label">Correct answers</label>--}}
    {{--                                <div class="row alternative_product_names_div">--}}
    {{--                                    <div class="col-md-9">--}}
    {{--                                        <input type="text" name="answer_option[]" class="form-control answer_option">--}}
    {{--                                        <span class="invalid-feedback" role="alert"></span>--}}
    {{--                                    </div>--}}
    {{--                                    <div class="col-md-1">--}}
    {{--                                        <input type="checkbox" name="answer_option_id[]" value=""--}}
    {{--                                               class="form-check-input fs-3 answer_option_id">--}}
    {{--                                        <input type="hidden" name="answer_option_id_bkp[]" class="answer_option_id_bkp"--}}
    {{--                                               value="0">--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                                <div class="answer_option_all">--}}
    {{--                                </div>--}}
    {{--                                <span class="invalid-feedback d-block" id="answer_option_idError" role="alert"></span>--}}
    {{--                                <div class="row mt-2">--}}
    {{--                                    <div class="col">--}}
    {{--                                        <button type="button" class="btn btn-success add_answer_option"><i--}}
    {{--                                                class="dripicons-plus"></i></button>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                        </div>--}}


    {{--                    </div>--}}
    {{--                    <div class="modal-footer">--}}
    {{--                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"--}}
    {{--                                aria-label="Close">@lang('translation.Close')</button>--}}
    {{--                        <button type="submit" class="btn btn-success waves-effect waves-light"--}}
    {{--                                id="saveBtn">@lang('translation.Save_changes')</button>--}}
    {{--                    </div>--}}
    {{--                </form>--}}
    {{--                --}}{{--  <form id="add-form" method="post" class="form-horizontal" action="{{ route('question.addupdate') }}">--}}
    {{--                @csrf--}}
    {{--                <input type="hidden" name="id" value="0" id="edit-id">--}}
    {{--                <div class="modal-body">--}}
    {{--                    <div class="row">--}}
    {{--                        <div class="col-md-12">--}}
    {{--                            <div class="mb-3">--}}
    {{--                                <label for="Text" class="form-label">Text</label>--}}
    {{--                                <input type="text" class="form-control text_question" value="{{ old('text_question') }}" name="text_question" id="text_question">--}}
    {{--                                <span class="invalid-feedback" id="text_questionError" data-ajax-feedback="text_question"role="alert"></span>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-md-12">--}}
    {{--                             <div class="mb-3">--}}
    {{--                                <label for="notes" class="form-label">Notes</label>--}}
    {{--                                <textarea  class="form-control" id="notes" name="notes" rows="3"></textarea>--}}
    {{--                                <span class="invalid-feedback" id="notesError" data-ajax-feedback="notes" role="alert"></span>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-md-12 mt-2">--}}
    {{--                            <div class="mb-3">--}}
    {{--                                <label for="question_type" class="form-label">Question type</label>--}}
    {{--                                <select name="question_type" id="question_type" class="form-select question_type">--}}
    {{--                                    <option value="">Select Question type</option>--}}
    {{--                                    @if ($questionType)--}}
    {{--                                        @foreach ($questionType as $key1 => $row1)--}}
    {{--                                        <option value="{{$key1}}">{{$row1}}</option>--}}
    {{--                                        @endforeach--}}
    {{--                                    @endif--}}
    {{--                                </select>--}}
    {{--                                <span class="invalid-feedback" id="question_typeError" data-ajax-feedback="question_type"--}}
    {{--                                role="alert"></span>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-md-12 mt-2">--}}
    {{--                            <div class="mb-3">--}}
    {{--                                <label for="answer_type" class="form-label">Answer type</label>--}}
    {{--                                <select name="answer_type" id="answer_type" class="form-select answer_type">--}}
    {{--                                    <option value="">Select Answer type</option>--}}
    {{--                                    <option value="1">Radio</option>--}}
    {{--                                    <option value="2">Multiple choice</option>--}}
    {{--                                </select>--}}
    {{--                                <span class="invalid-feedback" id="answer_typeError" data-ajax-feedback="answer_type"--}}
    {{--                                role="alert"></span>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-md-12 mt-2">--}}
    {{--                            <div class="mb-3">--}}
    {{--                                <label for="correct_answer_id" class="form-label">Correct answer</label>--}}
    {{--                                <input type="text" class="form-control correct_answer_id" value="{{ old('correct_answer_id') }}" name="correct_answer_id" id="correct_answer_id">--}}
    {{--                                <span class="invalid-feedback" id="correct_answer_idError" data-ajax-feedback="correct_answer_id"role="alert"></span>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-md-12 mt-2">--}}
    {{--                            <div class="mb-3">--}}
    {{--                                <label for="status" class="form-label">Status</label>--}}
    {{--                                <select name="status" id="status_type" class="form-select status">--}}
    {{--                                    <option value="1">Active</option>--}}
    {{--                                    <option value="0">Deactive</option>--}}
    {{--                                </select>--}}
    {{--                                <span class="invalid-feedback" id="statusError" data-ajax-feedback="status"--}}
    {{--                                role="alert"></span>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--                <div class="modal-footer">--}}
    {{--                    <button type="button" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal"--}}
    {{--                        aria-label="Close">@lang('translation.Close')</button>--}}
    {{--                    <button type="submit" class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>--}}
    {{--                </div>--}}
    {{--            </form> --}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    <div class="add_html_answer_option d-none">
        <div class="row answer_option_div mt-3">
            <div class="col-md-9">
                <input type="text" name="answer_option[]" class="form-control answer_option" value="">
                <span class="invalid-feedback" role="alert"></span>
            </div>
            <div class="col-md-1">
                <input type="checkbox" name="answer_option_id[]" value=""
                       class="form-check-input fs-3 answer_option_id">
                <input type="hidden" name="answer_option_id_bkp[]" class="answer_option_id_bkp" value="0">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove_answer_option"><i
                        class="mdi mdi-delete"></i></button>
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

    <script src="https://cdn.ckeditor.com/4.21.0/standard-all/ckeditor.js"></script>
    <script>
        // CKEDITOR.replace('editor');
        (function () {
            var mathElements = [
                'math',
                'maction',
                'maligngroup',
                'malignmark',
                'menclose',
                'merror',
                'mfenced',
                'mfrac',
                'mglyph',
                'mi',
                'mlabeledtr',
                'mlongdiv',
                'mmultiscripts',
                'mn',
                'mo',
                'mover',
                'mpadded',
                'mphantom',
                'mroot',
                'mrow',
                'ms',
                'mscarries',
                'mscarry',
                'msgroup',
                'msline',
                'mspace',
                'msqrt',
                'msrow',
                'mstack',
                'mstyle',
                'msub',
                'msup',
                'msubsup',
                'mtable',
                'mtd',
                'mtext',
                'mtr',
                'munder',
                'munderover',
                'semantics',
                'annotation',
                'annotation-xml'
            ];

            CKEDITOR.plugins.addExternal('ckeditor_wiris',
                'https://ckeditor.com/docs/ckeditor4/4.21.0/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');

            CKEDITOR.replace('editor', {
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
                height: 320,
                // Update the ACF configuration with MathML syntax.
                extraAllowedContent: mathElements.join(' ') +
                    '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
                removeButtons: 'PasteFromWord'
            });
        }());

        $(document).on('focusin', function (e) {
            if ($(e.target).hasClass('wrs_focusElement')) {
                e.stopImmediatePropagation();
            }
        });
    </script>
    <style>
        table.dataTable td,
        table.dataTable th {
            max-width: 100px !important;
            overflow: hidden !important;
        }

        .table-nowrap td,
        .table-nowrap th {
            white-space: pre-wrap !important;
        }
    </style>
    <script>
        {{--var apiUrl = "{{ session()->has('last_url') ? session('last_url') :route('question.list') }}";--}}
        var last_page = "{{ session('page') ?? 1 }}";
        var apiUrl = "{{ route('question.list') }}";
        var detailUrl = "{{ route('question.detail') }}";
        var deleteUrl = "{{ route('question.delete') }}";
        var addUrl = $('#add-form').attr('action');
        var baseUrl = "{{ asset('/') }}";
        var categories = @json($categories);
        var sub_categories = [];
        var topics = [];
        var exam_sections = [];
        var sub_sub_categories = [];
        $('.select2').select2()
        $(function () {

            $('#category_id').on('change', function () {
                let category_id = $(this).val();

                if (category_id == '') return;

                var __FOUND = categories.find(function (item, index) {
                    if (item.id == category_id)
                        return true;
                });
                sub_categories = __FOUND.sub_categories;
                topics = __FOUND.topics;
                exam_sections = __FOUND.exam_section;
                var options;
                options += `
						<option  value="">Select SubCategory</option>
						`
                let selected = "";
                $.each(sub_categories, function (index, value) {
{{--                    @if($session_sub_category_id)--}}
{{--                        selected = parseInt("{{$session_sub_category_id}}") == value.id ? "selected" : "";--}}
{{--                    @endif--}}
                        options += `<option value="${value.id}" ${selected}>${value.name}</option>`
                });
                $('#sub_category_id').html(options);


                options = '';
                options += `
						<option value="">Select Sub SubCategory</option>`
                $('#sub_subcategory_id').html(options);

                // topic select
                options = `<option value="">Select Topic</option>`;

                selected = "";
                $.each(topics, function (index, value) {
{{--                    @if(isset($session_questions_topic_id))--}}
{{--                        selected = parseInt("{{$session_questions_topic_id}}") == value.id ? "selected" : "";--}}
{{--                    @endif--}}

                        options += `<option  value="${value.id}" ${selected}>${value.topic}</option>`
                });
                $('#topic_id').html(options);

                // exam section select
                options = `<option value="">Select Exam Section</option>`;
                selected = "";
                $.each(exam_sections, function (index, value) {
{{--                    @if(isset($session_section_id))--}}
{{--                        selected = parseInt("{{$session_section_id}}") == value.id ? "selected" : "";--}}
{{--                    @endif--}}
                        options += `<option  value="${value.id}" ${selected}>${value.name}</option>`
                });
                $('#exam_section_id').html(options);


                setTimeout(() => {
{{--                    @if($session_sub_category_id)--}}
{{--                    $('#sub_category_id').trigger('change');--}}
{{--                    $('#topic_id').trigger('change');--}}
{{--                    $('#exam_section_id').trigger('change');--}}
{{--                    @endif--}}
                }, 300)
            })

            $('#sub_category_id').on('change', function () {
                let category_id = $(this).val();
                if (category_id == '') return;
                var __FOUND = sub_categories.find(function (item, index) {
                    if (item.id == category_id)
                        return true;
                });
                var options = "";
                options += `
						<option  value="">Select Sub SubCategory</option>
						`
                let selected = "";

                $.each(__FOUND.sub_categories, function (index, value) {
{{--                    @if($session_sub_subcategory_id)--}}
{{--                        selected = parseInt("{{$session_sub_subcategory_id}}") == value.id ? "selected" : "";--}}
{{--                    @endif--}}

                        options += `
						<option  value="${value.id}" ${selected}>${value.name}</option>
						`
                });
                console.log(options)
                $('#sub_subcategory_id').html(options);
{{--                @if($session_sub_subcategory_id)--}}
{{--                $('#sub_subcategory_id').trigger('change');--}}
{{--                @endif--}}

            })
        })

{{--        @if(isset($session_category_id))--}}
{{--        setTimeout(() => {--}}
{{--            $('#category_id').trigger('change');--}}
{{--        }, 400)--}}
{{--        @endif--}}
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('question.js') }}?v=4"></script>
@endsection
