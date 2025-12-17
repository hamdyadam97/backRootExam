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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Category</label>
                                    <select required name="category_id" id="category_id"
                                            class="form-control select2 category_id"
                                            placeholder="@lang('Select Category')">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ isset($item) && $item->category_id == $category->id ? 'selected': '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="category_idError"
                                          data-ajax-feedback="category_id"
                                          role="alert"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Text" class="form-label">Title</label>
                                    <input class="form-control" name="title" value="{{ @$item->title }}"/>
                                    <span class="invalid-feedback" id="titleError"
                                          data-ajax-feedback="title" role="alert"></span>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="view-question-image" style="{{ isset($item) ? "" : "display: none;" }}">
                                        <img src="{{ isset($item)?asset('storage/blogs_image/'.$item->image) : "" }}"
                                             class="question_img" height="50px;">
                                    </div>
                                    <label for="hint" class="form-label">Image</label>
                                    <input id="question_image" type="file" class="form-control question_image"
                                           name="image" title="Image">
                                    <span class="invalid-feedback" id="imageError"
                                          data-ajax-feedback="image" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status_type" class="form-select status">
                                        <option value="1"  {{ isset($item) && $item->status == 1 ? 'selected': '' }}>Active</option>
                                        <option value="0"  {{ isset($item) && $item->status == 0 ? 'selected': '' }}>Deactive</option>
                                    </select>
                                    <span class="invalid-feedback" id="statusError" data-ajax-feedback="status"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <label class="form-label">Description</label>
                            <div class="col-md-12">
                                <textarea id="description" class="form-control description mt-2"
                                          name="description" rows="3">{{@$item->description}}</textarea>
                                <span class="invalid-feedback" id="description}Error"
                                      data-ajax-feedback="description" role="alert"></span>
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
            CKEDITOR.replace('description', {
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
        var apiUrl = "{{ route('blogs.list') }}";
        var addUrl = $('#add-form').attr('action');
    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('blogs.js') }}"></script>
@endsection
