@extends('layouts.master')
@section('title')
    @lang('Blogs')
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

    <!-- Select2 Css -->
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form id="add-form" method="post" class="form-horizontal"
                          action="{{ url()->current() }}">
                        @csrf






                        <div class="row">
                            <label class="form-label">Text</label>
                            <div class="col-md-12">
                                <textarea id="lab_value" class="form-control lab_value mt-2"
                                          name="lab_value" rows="3">{{@$settings->valueOf('lab_value')}}</textarea>
                                <span class="invalid-feedback" id="lab_value}Error"
                                      data-ajax-feedback="lab_value" role="alert"></span>
                            </div>
                        </div>
                        <a href="{{ route('blogs.index') }}"
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
    <!-- Datatable js -->
    <script src="{{ asset('/assets/libs/datatables/datatables.min.js') }}"></script>


    <script src="{{ asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ asset('/assets/libs/select2/select2.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('ckeditor/ckeditor.js') }}"></script>

    {{--    <script src="https://cdn.ckeditor.com/4.21.0/standard-all/ckeditor.js"></script>--}}
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

            // for correct answer editor
            CKEDITOR.replace('lab_value', {
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


        $('.select2').select2()

    </script>

    <script>
        var addUrl = $('#add-form').attr('action');
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('blogs.js') }}"></script>
@endsection
