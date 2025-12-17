@extends('layouts.master')
@section('title')
@lang('Questions')
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
                
                <form method="post" class="form-horizontal" action="{{ route('question.importFile') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="" id="edit-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="csv_file" class="form-label">Import</label>
                                    <input type="file" id="csv_file" class="form-control hint" name="csv_file" value="{{ old('csv_file') }}">
                                    <span class="invalid-feedback" id="hintError" data-ajax-feedback="hint" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <a href="{{route('question')}}" class="btn btn-default waves-effect mt-2">@lang('translation.Close')</a>

                        <button type="submit" class="btn btn-success waves-effect waves-light mt-2" id="importBtn">Import'</button>
                </form>
            </div>
        </div>
    </div>
</div>
  

<div class="add_html_answer_option d-none">
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
    (function() {
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

      CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://ckeditor.com/docs/ckeditor4/4.21.0/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');

      // for notes
      CKEDITOR.replace('editor', {
        extraPlugins: 'ckeditor_wiris',
        // For now, MathType is incompatible with CKEditor file upload plugins.
        removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
        height: 320,
        // Update the ACF configuration with MathML syntax.
        extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
        removeButtons: 'PasteFromWord'
      });

      // for text
      CKEDITOR.replace('textEditor', {
        extraPlugins: 'ckeditor_wiris',
        // For now, MathType is incompatible with CKEditor file upload plugins.
        removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
        height: 320,
        // Update the ACF configuration with MathML syntax.
        extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)',
        removeButtons: 'PasteFromWord'
      });

    }());

    $(document).on('focusin', function(e) {
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
</script>
@endsection
@section('script-bottom')
<script src="{{ addPageJsLink('question.js') }}"></script>
@endsection