@extends('layouts.master')
@section('title')
    @lang('Questions')
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

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php
                    // print_r($question_answers);
                    $exam_id = 0;
                    if (isset($question_answers->questions_answers)) {
                        foreach ($question_answers->questions_answers as $list) {
                            $exam_id = $list->exam_id;
                        }
                    }
                    ?>
                    <form id="add-form" method="post" class="form-horizontal"
                          action="{{ route('question.addupdate') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}" id="edit-id">
                        <div class="row">
                            {{--                        <div class="col-md-6"> --}}
                            {{--                            <div class="mb-3"> --}}
                            {{--                                <label for="Text" class="form-label">Exam</label> --}}
                            {{--                                <select name="exam_id" id="exam_id" class="form-control select2 exam_id" placeholder="@lang('Select Exam')"> --}}
                            {{--                                    <option value="">Select Exam</option> --}}
                            {{--                                    @foreach ($exams as $exam) --}}
                            {{--                                        <option @if ($exam_id == $exam->id) selected @endif value="{{ $exam->id }}">{{ $exam->title }}</option> --}}
                            {{--                                    @endforeach --}}
                            {{--                                </select> --}}
                            {{--                                <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="text_question"role="alert"></span> --}}
                            {{--                            </div> --}}
                            {{--                        </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-control select2 category_id"
                                            placeholder="@lang('Select Category')">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $category->id == $question_answers->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="category_idError"
                                          data-ajax-feedback="category_id"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id"
                                            class="form-control select2 category_id"
                                            placeholder="@lang('Select Sub Category')">
                                        <option value="">Select SubCategory</option>

                                    </select>
                                    <span class="invalid-feedback" id="sub_category_idError"
                                          data-ajax-feedback="sub_category_id" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Sub SubCategory</label>
                                    <select name="sub_subcategory_id" id="sub_subcategory_id"
                                            class="form-control select2 category_id"
                                            placeholder="@lang('Select Sub SubCategory')">
                                        <option value="">Select Sub SubCategory</option>

                                    </select>
                                    <span class="invalid-feedback" id="sub_subcategory_idError"
                                          data-ajax-feedback="sub_subcategory_id" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Exam Section</label>
                                    <select name="section_id[]" id="section_id" class="form-select select2 exam_id"
                                            multiple
                                            placeholder="@lang('Select Exam Section')">
                                        <option value="">Select Exam Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}"
                                            @if (in_array($section->id, $exam_sections))
                                                {{ 'selected' }}
                                                @endif>
                                                {{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="section_idError"
                                          data-ajax-feedback="text_question" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Text</label>
                                    <textarea id="text_question" class="form-control" name="text_question" rows="3">
                                </textarea>
                                    <span class="invalid-feedback" id="text_questionError"
                                          data-ajax-feedback="text_question" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea id="editor" class="form-control" name="notes" rows="3">
                                </textarea>
                                    <span class="invalid-feedback" id="notesError" data-ajax-feedback="notes"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="question_type" class="form-label">Question type</label>
                                    <select name="question_type" id="question_type" class="form-select question_type">
                                        @if ($questionType)
                                            @foreach ($questionType as $key1 => $row1)
                                                <option value="{{ $key1 }}">{{ $row1 }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="invalid-feedback" id="question_typeError"
                                          data-ajax-feedback="question_type" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="answer_type" class="form-label">Answer type</label>
                                    <select name="answer_type" id="answer_type" class="form-select answer_type">
                                        <option value="1">Radio</option>
                                        <option value="2">Multiple choice</option>
                                    </select>
                                    <span class="invalid-feedback" id="answer_typeError"
                                          data-ajax-feedback="answer_type"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="hint" class="form-label">Hint</label>
                                    <input id="hint" type="text" class="form-control hint" name="hint"
                                           value="{{ old('hint') }}">
                                    <span class="invalid-feedback" id="hintError" data-ajax-feedback="hint"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="show_hint" class="form-label">Show Hint</label>
                                    <select name="show_hint" id="show_hint"
                                            class="form-select general-select show_hint">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <span class="invalid-feedback" id="show_hintError" data-ajax-feedback="show_hint"
                                          role="alert"></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="video_link" class="form-label">Video Link</label>
                                    <input id="video_link" type="text" class="form-control video_link"
                                           name="video_link" value="{{ old('video_link') }}">
                                    <span class="invalid-feedback" id="video_linkError" data-ajax-feedback="video_link"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="show_video" class="form-label">Show Video</label>
                                    <select name="show_video" id="show_video"
                                            class="form-select general-select show_video">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <span class="invalid-feedback" id="show_videoError" data-ajax-feedback="show_video"
                                          role="alert"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="time_minutes" class="form-label">Time in Minutes</label>
                                    {{-- <textarea  class="form-control" id="time_minutes" name="time_minutes" rows="3"></textarea> --}}
                                    <input id="time_minutes" type="number" class="form-control time_minutes"
                                           name="time_minutes" value="{{ old('time_minutes') }}">
                                    <span class="invalid-feedback" id="time_minutesError"
                                          data-ajax-feedback="time_minutes" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="view-question-image" style="display: none;">
                                        <img src="" class="question_img" height="50px;">
                                    </div>
                                    <label for="hint" class="form-label">Question Image</label>
                                    <input id="question_image" type="file" class="form-control question_image"
                                           name="question_image" title="question_image">
                                    <span class="invalid-feedback" id="question_imageError"
                                          data-ajax-feedback="question_image" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="show_hint" class="form-label">Show Question Image</label>
                                    <select name="question_has_image" id="question_has_image"
                                            class="form-select general-select show_hint">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <span class="invalid-feedback" id="question_has_imageError"
                                          data-ajax-feedback="question_has_image" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="view-answer-image" style="display: none;">
                                        <img src="" class="answer_img" height="50px;">
                                    </div>
                                    <label for="hint" class="form-label">Answer Image</label>
                                    <input id="answer_image" type="file" class="form-control answer_image"
                                           name="answer_image" title="answer_image">
                                    <span class="invalid-feedback" id="answer_imageError"
                                          data-ajax-feedback="question_image" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="answer_has_image" class="form-label">Show Answer Image</label>
                                    <select name="answer_has_image" id="answer_has_image"
                                            class="form-select general-select show_hint">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <span class="invalid-feedback" id="answer_has_imageError"
                                          data-ajax-feedback="answer_has_image" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="show_answer" class="form-label">Show Answer</label>
                                    <select name="show_answer" id="show_answer"
                                            class="form-select general-select show_answer">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <span class="invalid-feedback" id="show_answerError"
                                          data-ajax-feedback="show_answer"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="show_answer_explanation" class="form-label">
                                        Show Answer Explanation
                                    </label>
                                    <select name="show_answer_explanation" id="show_answer_explanation"
                                            class="form-select general-select show_answer">
                                        <option value="0"
                                        @if ($question_answers->show_answer_explanation == '0')
                                            {{ 'selected' }}
                                            @endif>No
                                        </option>
                                        <option value="1"
                                        @if ($question_answers->show_answer_explanation == '1')
                                            {{ 'selected' }}
                                            @endif>Yes
                                        </option>
                                    </select>
                                    <span class="invalid-feedback" id="show_answerError"
                                          data-ajax-feedback="show_answer"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="questions_topic_id" class="form-label">
                                        Question topic
                                    </label>
                                    <select name="questions_topic_id[]" id="questions_topic_id"
                                            class="form-select select2 general-select show_answer" multiple>
                                        <option value="">Select Topic</option>
                                        @foreach ($questionTopics as $questionTopic)
                                            <option value="{{ $questionTopic->id }}"
                                                {{ in_array($questionTopic->id, $question_topic) ? 'selected' : '' }}>
                                                {{ $questionTopic->topic }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="form-label">Correct answers</label>
                                @php
                                    $exp = explode(',', $question_answers->correct_answer_id);
                                @endphp
                                <?php $i = 1; ?>
                                @for ($i = 0; $i <= 4; $i++)
                                    <div class="col-md-6">
                                        <input type="checkbox" name="answer_option_id[]" value=""
                                               class="form-check-input fs-3 answer_option_id mt-2"
                                        @if (in_array($question_answers->questions_answers[$i]->id, $exp))
                                            {{ 'checked' }}
                                            @endif>
                                        <input type="hidden" name="answer_opt_ids[]"
                                               value="{{ $question_answers->questions_answers[$i]->id }}">
                                        <input type="hidden" name="answer_option_id_bkp[]" class="answer_option_id_bkp"
                                               value="@if (in_array($question_answers->questions_answers[$i]->id, $exp)) {{ '1' }} @else {{ '0' }} @endif">
                                        <textarea id="correct_answer_editor{{ $i }}"
                                                  class="form-control correct_answer_editor mt-2"
                                                  name="correct_answer_editor[]"
                                                  rows="3">{{ $question_answers->questions_answers[$i]->answer_option }}</textarea>
                                        <span class="invalid-feedback" id="correct_answer_editor{{ $i }}Error"
                                              data-ajax-feedback="correct_answer_editor{{ $i }}" role="alert"></span>
                                    </div>
                                @endfor
                                {{-- <div class="row alternative_product_names_div">
                                <div class="col-md-9">
                                    <input type="text" name="answer_option[]" class="form-control answer_option">
                                    <span class="invalid-feedback" role="alert"></span>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="answer_option_id[]" value="" class="form-check-input fs-3 answer_option_id" >
                                    <input type="hidden" name="answer_option_id_bkp[]" class="answer_option_id_bkp" value="0">
                                </div>
                            </div>
                            <div class="answer_option_all">
                            </div>
                            <span class="invalid-feedback d-block" id="answer_option_idError" role="alert"></span>
                            <div class="row mt-2">
                                <div class="col">
                                    <button type="button" class="btn btn-success add_answer_option"><i class="dripicons-plus"></i></button>
                                </div>
                            </div> --}}
                            </div>
                        </div>
                        <a href="{{ route('question') }}"
                           class="btn btn-default waves-effect mt-2">@lang('translation.Close')</a>
                        <button type="submit" class="btn btn-success waves-effect waves-light mt-2"
                                id="saveBtn">@lang('translation.Save_changes')</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end col -->
    </div>

    {{-- <div class="add_html_answer_option d-none">
    <div class="row answer_option_div mt-3">
        <div class="col-md-9">
            <input type="text" name="answer_option[]" class="form-control answer_option" value="">
            <span class="invalid-feedback" role="alert"></span>
        </div>
        <div class="col-md-1">
            <input type="checkbox" name="answer_option_id[]" value="" class="form-check-input fs-3 answer_option_id" >
            <input type="hidden" name="answer_option_id_bkp[]" class="answer_option_id_bkp" value="0">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove_answer_option"><i class="mdi mdi-delete"></i></button>
        </div>
    </div>
</div> --}}

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

    <script type="text/javascript" src="{{ asset('ckeditor/ckeditor.js') }}"></script>
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

            //for notes
            CKEDITOR.replace('editor', {
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
                height: 320,
                contentsLangDirection: 'ltr',
                // Update the ACF configuration with MathML syntax.
                extraAllowedContent: mathElements.join(' ') +
                    '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
                removeButtons: 'PasteFromWord'
            });

            // for correct answer editor
            var i = 1;
            for (i = 0; i <= 4; i++) {
                CKEDITOR.replace('correct_answer_editor' + i, {
                    extraPlugins: 'ckeditor_wiris',
                    // For now, MathType is incompatible with CKEditor file upload plugins.
                    removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
                    height: 320,
                    contentsLangDirection: 'ltr',
                    // Update the ACF configuration with MathML syntax.
                    extraAllowedContent: mathElements.join(' ') +
                        '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
                    removeButtons: 'PasteFromWord'
                });
            }

            // for text
            CKEDITOR.replace('text_question', {
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
                height: 320,
                contentsLangDirection: 'ltr',
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
        var categories = @json($categories);
        var sub_categories = [];
        var sub_sub_categories = [];
        var topics = [];
        var exam_sections = [];

        $(function () {

            $('#category_id').on('change', function () {
                let category_id = $(this).val();
                if (category_id == '') return;
                var __FOUND = categories.find(function (item, index) {
                    if (item.id == category_id)
                        return true;
                });
                var selected
                sub_categories = __FOUND.sub_categories;
                topics = __FOUND.topics;
                exam_sections = __FOUND.exam_section;

                var options;
                options += `
						<option  value="">Select SubCategory</option>
						`
                $.each(__FOUND.sub_categories, function (index, value) {
                    selected = parseInt(value.id) == parseInt(
                        '{{ $question_answers->sub_category_id }}') ? 'selected' : ''
                    options += `
						<option ${selected} value="${value.id}">${value.name}</option>
						`
                });
                $('#sub_category_id').html(options);
                options = '';
                options += `
						<option value="">Select Sub SubCategory</option>`
                $('#sub_subcategory_id').html(options);

                setTopicsAndSections(topics, exam_sections);

            })
            $('#sub_category_id').on('change', function () {
                let category_id = $(this).val();
                if (category_id == '') return;
                var __FOUND = sub_categories.find(function (item, index) {
                    if (item.id == category_id)
                        return true;
                });
                var selected
                var options;
                options += `<option  value="">Select Sub SubCategory</option>`;


                $.each(__FOUND.sub_categories, function (index, value) {
                    selected = parseInt(value.id) == parseInt(
                        '{{ $question_answers->sub_subcategory_id }}') ? 'selected' : ''
                    options += `
						<option  ${selected} value="${value.id}">${value.name}</option>
						`
                });
                $('#sub_subcategory_id').html(options);


            })

            function setTopicsAndSections(topics, exam_sections) {
                // topic select
                options = `<option value="">Select Topic</option>`;

                $("#questions_topic_id").val([]).trigger("change");
                let selected_topics = {{ json_encode($question_topic) }};
                $.each(topics, function (index, value) {
                    selected = selected_topics.includes(parseInt(value.id)) ? 'selected' : '';
                    options += `<option  ${selected} value="${value.id}">${value.topic}</option>`
                });

                $('#questions_topic_id').html(options);

                // exam section select
                options = `<option value="">Select Exam Section</option>`;

                $('#section_id').val([]).trigger("change");
                let selected_sections = {{ json_encode($exam_sections) }};
                $.each(exam_sections, function (index, value) {
                    selected = selected_sections.includes(parseInt(value.id)) ? 'selected' : '';
                    options += `<option ${selected} value="${value.id}">${value.name}</option>`
                });
                $('#section_id').html(options);

            }

            $('#category_id').change()
            $('#sub_category_id').change()
            $('.select2').select2()

        })
    </script>

    <script>
        var apiUrl = "{{ route('question.list') }}";
        var detailUrl = "{{ route('question.detail') }}";
        var deleteUrl = "{{ route('question.delete') }}";
        var addUrl = $('#add-form').attr('action');
        var listUrl = "{{ route('question') }}";

        //
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('question.js') }}?v=2"></script>
    <script type="text/javascript">
        var json = <?php echo json_encode($question_answers); ?>;

        $(function () {
            setdata(json);
        })
    </script>
@endsection
