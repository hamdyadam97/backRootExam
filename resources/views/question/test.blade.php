@extends('layouts.master')
@section('title')
    @lang('Test Questions')
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

    <!-- Summernote CSS -->
    {{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">--}}
    {{--    <!-- MathType CSS -->--}}
    {{--    <link rel="stylesheet" href="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image">--}}

@endpush
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

                        <a href="{{ route('question') }}"
                           class="btn btn-default waves-effect mt-2">@lang('translation.Close')</a>
                        <button type="submit" class="btn btn-success waves-effect waves-light mt-2"
                                id="saveBtn">@lang('translation.Save_changes')</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>

    <!-- jQuery -->
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    <!-- MathType JS -->
    <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>


    <script src="https://cdn.ckeditor.com/4.21.0/standard-all/ckeditor.js"></script>
    <script>
        // CKEDITOR.replace('editor');
        (function () {
            // var mathElements = [
            //     'math',
            //     'maction',
            //     'maligngroup',
            //     'malignmark',
            //     'menclose',
            //     'merror',
            //     'mfenced',
            //     'mfrac',
            //     'mglyph',
            //     'mi',
            //     'mlabeledtr',
            //     'mlongdiv',
            //     'mmultiscripts',
            //     'mn',
            //     'mo',
            //     'mover',
            //     'mpadded',
            //     'mphantom',
            //     'mroot',
            //     'mrow',
            //     'ms',
            //     'mscarries',
            //     'mscarry',
            //     'msgroup',
            //     'msline',
            //     'mspace',
            //     'msqrt',
            //     'msrow',
            //     'mstack',
            //     'mstyle',
            //     'msub',
            //     'msup',
            //     'msubsup',
            //     'mtable',
            //     'mtd',
            //     'mtext',
            //     'mtr',
            //     'munder',
            //     'munderover',
            //     'semantics',
            //     'annotation',
            //     'annotation-xml'
            // ];
            //
            // CKEDITOR.plugins.addExternal('ckeditor_wiris',
            //     'https://ckeditor.com/docs/ckeditor4/4.21.0/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');
            //
            // // for notes
            // CKEDITOR.replace('editor', {
            //     extraPlugins: 'ckeditor_wiris',
            //     // For now, MathType is incompatible with CKEditor file upload plugins.
            //     removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
            //     height: 320,
            //     contentsLangDirection: 'ltr',
            //     // Update the ACF configuration with MathML syntax.
            //     extraAllowedContent: mathElements.join(' ') +
            //         '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
            //     removeButtons: 'PasteFromWord'
            // });
            //
            // // for correct answer editor
            // var i = 1;
            // for (i = 0; i <= 4; i++) {
            //     CKEDITOR.replace('correct_answer_editor' + i, {
            //         extraPlugins: 'ckeditor_wiris',
            //         // For now, MathType is incompatible with CKEditor file upload plugins.
            //         removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
            //         height: 320,
            //         contentsLangDirection: 'ltr',
            //         // Update the ACF configuration with MathML syntax.
            //         extraAllowedContent: mathElements.join(' ') +
            //             '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
            //         removeButtons: 'PasteFromWord'
            //     });
            // }

            $('#text_question').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                    ['math', ['math']] // Add MathType button
                ],
                buttons: {
                    math: function (context) {
                        var ui = $.summernote.ui;
                        // create button
                        var button = ui.button({
                            contents: '<i class="note-icon-pencil"/> MathType',
                            tooltip: 'Insert Math',
                            click: function () {
                                console.log(123)
                                WirisPlugin.currentInstance.insertFormula();
                            }
                        });
                        return button.render(); // return button as jquery object
                    }
                }
            });

            // for text
            // CKEDITOR.replace('text_question', {
            //     extraPlugins: 'ckeditor_wiris',
            //     // For now, MathType is incompatible with CKEditor file upload plugins.
            //     removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
            //     height: 320,
            //     contentsLangDirection: 'ltr',
            //     // Update the ACF configuration with MathML syntax.
            //     extraAllowedContent: mathElements.join(' ') +
            //         '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
            //     removeButtons: 'PasteFromWord'
            // });
        }());

        $(document).on('focusin', function (e) {
            if ($(e.target).hasClass('wrs_focusElement')) {
                e.stopImmediatePropagation();
            }
        });


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


{{--@section('script')--}}
{{--    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>--}}
{{--    <!-- Datatable js -->--}}
{{--    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>--}}
{{--    <!-- Datepicker Css -->--}}
{{--    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>--}}
{{--    <!-- Inputmask js -->--}}
{{--    --}}{{-- <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script> --}}
{{--    <!-- Sweet Alerts js -->--}}
{{--    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>--}}
{{--    <!-- Select2 js -->--}}
{{--    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>--}}

{{--    <!-- jQuery -->--}}
{{--    <!-- Bootstrap JS -->--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>--}}
{{--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>--}}
{{--    <!-- Summernote JS -->--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>--}}
{{--    <!-- MathType JS -->--}}
{{--    <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>--}}


{{--    <script src="https://cdn.ckeditor.com/4.21.0/standard-all/ckeditor.js"></script>--}}
{{--    <script>--}}
{{--        // CKEDITOR.replace('editor');--}}
{{--        (function () {--}}
{{--            var mathElements = [--}}
{{--                'math',--}}
{{--                'maction',--}}
{{--                'maligngroup',--}}
{{--                'malignmark',--}}
{{--                'menclose',--}}
{{--                'merror',--}}
{{--                'mfenced',--}}
{{--                'mfrac',--}}
{{--                'mglyph',--}}
{{--                'mi',--}}
{{--                'mlabeledtr',--}}
{{--                'mlongdiv',--}}
{{--                'mmultiscripts',--}}
{{--                'mn',--}}
{{--                'mo',--}}
{{--                'mover',--}}
{{--                'mpadded',--}}
{{--                'mphantom',--}}
{{--                'mroot',--}}
{{--                'mrow',--}}
{{--                'ms',--}}
{{--                'mscarries',--}}
{{--                'mscarry',--}}
{{--                'msgroup',--}}
{{--                'msline',--}}
{{--                'mspace',--}}
{{--                'msqrt',--}}
{{--                'msrow',--}}
{{--                'mstack',--}}
{{--                'mstyle',--}}
{{--                'msub',--}}
{{--                'msup',--}}
{{--                'msubsup',--}}
{{--                'mtable',--}}
{{--                'mtd',--}}
{{--                'mtext',--}}
{{--                'mtr',--}}
{{--                'munder',--}}
{{--                'munderover',--}}
{{--                'semantics',--}}
{{--                'annotation',--}}
{{--                'annotation-xml'--}}
{{--            ];--}}

{{--            CKEDITOR.plugins.addExternal('ckeditor_wiris',--}}
{{--                'https://ckeditor.com/docs/ckeditor4/4.21.0/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');--}}

{{--            // for notes--}}
{{--            CKEDITOR.replace('editor', {--}}
{{--                extraPlugins: 'ckeditor_wiris',--}}
{{--                // For now, MathType is incompatible with CKEditor file upload plugins.--}}
{{--                removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',--}}
{{--                height: 320,--}}
{{--                contentsLangDirection: 'ltr',--}}
{{--                // Update the ACF configuration with MathML syntax.--}}
{{--                extraAllowedContent: mathElements.join(' ') +--}}
{{--                    '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',--}}
{{--                removeButtons: 'PasteFromWord'--}}
{{--            });--}}

{{--            // for correct answer editor--}}
{{--            var i = 1;--}}
{{--            for (i = 0; i <= 4; i++) {--}}
{{--                CKEDITOR.replace('correct_answer_editor' + i, {--}}
{{--                    extraPlugins: 'ckeditor_wiris',--}}
{{--                    // For now, MathType is incompatible with CKEditor file upload plugins.--}}
{{--                    removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',--}}
{{--                    height: 320,--}}
{{--                    contentsLangDirection: 'ltr',--}}
{{--                    // Update the ACF configuration with MathML syntax.--}}
{{--                    extraAllowedContent: mathElements.join(' ') +--}}
{{--                        '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',--}}
{{--                    removeButtons: 'PasteFromWord'--}}
{{--                });--}}
{{--            }--}}

{{--            $('#text_question').summernote({--}}
{{--                height: 300,--}}
{{--                toolbar: [--}}
{{--                    ['style', ['style']],--}}
{{--                    ['font', ['bold', 'italic', 'underline', 'clear']],--}}
{{--                    ['fontname', ['fontname']],--}}
{{--                    ['color', ['color']],--}}
{{--                    ['para', ['ul', 'ol', 'paragraph']],--}}
{{--                    ['height', ['height']],--}}
{{--                    ['table', ['table']],--}}
{{--                    ['insert', ['link', 'picture', 'video']],--}}
{{--                    ['view', ['fullscreen', 'codeview', 'help']],--}}
{{--                    ['math', ['math']] // Add MathType button--}}
{{--                ],--}}
{{--                buttons: {--}}
{{--                    math: function(context) {--}}
{{--                        var ui = $.summernote.ui;--}}
{{--                        // create button--}}
{{--                        var button = ui.button({--}}
{{--                            contents: '<i class="note-icon-pencil"/> MathType',--}}
{{--                            tooltip: 'Insert Math',--}}
{{--                            click: function() {--}}
{{--                                console.log(123)--}}
{{--                                WirisPlugin.currentInstance.insertFormula();--}}
{{--                            }--}}
{{--                        });--}}
{{--                        return button.render(); // return button as jquery object--}}
{{--                    }--}}
{{--                }--}}
{{--            });--}}

{{--            // for text--}}
{{--            // CKEDITOR.replace('text_question', {--}}
{{--            //     extraPlugins: 'ckeditor_wiris',--}}
{{--            //     // For now, MathType is incompatible with CKEditor file upload plugins.--}}
{{--            //     removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',--}}
{{--            //     height: 320,--}}
{{--            //     contentsLangDirection: 'ltr',--}}
{{--            //     // Update the ACF configuration with MathML syntax.--}}
{{--            //     extraAllowedContent: mathElements.join(' ') +--}}
{{--            //         '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',--}}
{{--            //     removeButtons: 'PasteFromWord'--}}
{{--            // });--}}
{{--        }());--}}

{{--        $(document).on('focusin', function (e) {--}}
{{--            if ($(e.target).hasClass('wrs_focusElement')) {--}}
{{--                e.stopImmediatePropagation();--}}
{{--            }--}}
{{--        });--}}
{{--        var categories = @json($categories);--}}
{{--        var sub_categories = [];--}}
{{--        var sub_sub_categories = [];--}}
{{--        var topics = [];--}}
{{--        var exam_sections = [];--}}

{{--        $(function () {--}}

{{--            $('#category_id').on('change', function () {--}}
{{--                let category_id = $(this).val();--}}
{{--                if (category_id == '') return;--}}
{{--                var __FOUND = categories.find(function (item, index) {--}}
{{--                    if (item.id == category_id)--}}
{{--                        return true;--}}
{{--                });--}}
{{--                var selected--}}
{{--                sub_categories = __FOUND.sub_categories;--}}
{{--                topics = __FOUND.topics;--}}
{{--                exam_sections = __FOUND.exam_section;--}}

{{--                var options;--}}
{{--                options += `--}}
{{--						<option  value="">Select SubCategory</option>--}}
{{--						`--}}
{{--                $.each(__FOUND.sub_categories, function (index, value) {--}}
{{--                    selected = parseInt(value.id) == parseInt(--}}
{{--                        '{{ $question_answers->sub_category_id }}') ? 'selected' : ''--}}
{{--                    options += `--}}
{{--						<option ${selected} value="${value.id}">${value.name}</option>--}}
{{--						`--}}
{{--                });--}}
{{--                $('#sub_category_id').html(options);--}}
{{--                options = '';--}}
{{--                options += `--}}
{{--						<option value="">Select Sub SubCategory</option>`--}}
{{--                $('#sub_subcategory_id').html(options);--}}

{{--                setTopicsAndSections(topics, exam_sections);--}}

{{--            })--}}
{{--            $('#sub_category_id').on('change', function () {--}}
{{--                let category_id = $(this).val();--}}
{{--                if (category_id == '') return;--}}
{{--                var __FOUND = sub_categories.find(function (item, index) {--}}
{{--                    if (item.id == category_id)--}}
{{--                        return true;--}}
{{--                });--}}
{{--                var selected--}}
{{--                var options;--}}
{{--                options += `<option  value="">Select Sub SubCategory</option>`;--}}


{{--                $.each(__FOUND.sub_categories, function (index, value) {--}}
{{--                    selected = parseInt(value.id) == parseInt(--}}
{{--                        '{{ $question_answers->sub_subcategory_id }}') ? 'selected' : ''--}}
{{--                    options += `--}}
{{--						<option  ${selected} value="${value.id}">${value.name}</option>--}}
{{--						`--}}
{{--                });--}}
{{--                $('#sub_subcategory_id').html(options);--}}


{{--            })--}}

{{--            function setTopicsAndSections(topics, exam_sections) {--}}
{{--                // topic select--}}
{{--                options = `<option value="">Select Topic</option>`;--}}

{{--                $("#questions_topic_id").val([]).trigger("change");--}}
{{--                let selected_topics = {{ json_encode($question_topic) }};--}}
{{--                $.each(topics, function (index, value) {--}}
{{--                    selected = selected_topics.includes(parseInt(value.id)) ? 'selected' : '';--}}
{{--                    options += `<option  ${selected} value="${value.id}">${value.topic}</option>`--}}
{{--                });--}}

{{--                $('#questions_topic_id').html(options);--}}

{{--                // exam section select--}}
{{--                options = `<option value="">Select Exam Section</option>`;--}}

{{--                $('#section_id').val([]).trigger("change");--}}
{{--                let selected_sections = {{ json_encode($exam_sections) }};--}}
{{--                $.each(exam_sections, function (index, value) {--}}
{{--                    selected = selected_sections.includes(parseInt(value.id)) ? 'selected' : '';--}}
{{--                    options += `<option ${selected} value="${value.id}">${value.name}</option>`--}}
{{--                });--}}
{{--                $('#section_id').html(options);--}}

{{--            }--}}

{{--            $('#category_id').change()--}}
{{--            $('#sub_category_id').change()--}}
{{--            $('.select2').select2()--}}

{{--        })--}}
{{--    </script>--}}

{{--    <script>--}}
{{--        var apiUrl = "{{ route('question.list') }}";--}}
{{--        var detailUrl = "{{ route('question.detail') }}";--}}
{{--        var deleteUrl = "{{ route('question.delete') }}";--}}
{{--        var addUrl = $('#add-form').attr('action');--}}
{{--        var listUrl = "{{ route('question') }}";--}}

{{--        //--}}
{{--    </script>--}}
{{--@endsection--}}
@section('script-bottom')
    {{--    <script src="{{ addPageJsLink('question.js') }}?v=2"></script>--}}
    {{--    <script type="text/javascript">--}}
    {{--        var json = <?php echo json_encode($question_answers); ?>;--}}

    {{--        $(function() {--}}
    {{--            setdata(json);--}}
    {{--        })--}}
    {{--    </script>--}}
@endsection
