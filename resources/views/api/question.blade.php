<!DOCTYPE html>
<html lang="en">

<head>
    <title>MathType for CKEditor | Math & Science</title>


    <script type="text/javascript" src="{{ asset('samer/ckeditor4/plugins/ckeditor_wiris/integration/WIRISplugins.js?viewer=image') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>
    <script type="text/javascript">
        if (window.location.search !== '') {
            var urlParams = window.location.search;
            if (urlParams[0] == '?') {
                urlParams = urlParams.substr(1, urlParams.length);
                urlParams = urlParams.split('&');
                for (i = 0; i < urlParams.length; i = i + 1) {
                    var paramVariableName = urlParams[i].split('=')[0];
                    if (paramVariableName === 'language') {
                        _wrs_int_langCode = urlParams[i].split('=')[1];
                        break;
                    }
                }
            }
        }
    </script>
    <!-- Editor Plugin -->
    <link type="text/css" rel="stylesheet" href="''"/>
    <script type="text/javascript" src="{{ asset('samer/ckeditor4/ckeditor.js') }}"></script>

    <!-- Style for html code -->
    <link type="text/css" rel="stylesheet" href="{{ asset('samer/css/prism.css') }}" />

    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('samer/css/style.css') }}">

    <!-- Roboto Font -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <!-- Extra -->


</head>

<body>
{{--<div id="skiptocontent"><a href="#editorContainer">Skip to EDITOR</a></div> --}}

<div class="wrs_content">
    <div class="wrs_container">
        <div class="wrs_row" style="display: none;">
            <div class="wrs_col wrs_s12">
                <div id="editorContainer">
                    <div id="toolbarLocation"></div>
                    <div id="example" class="wrs_div_box" tabindex="0" spellcheck="false" role="textbox" aria-label="Rich Text Editor, example" title="Rich Text Editor, example">
                        {!! $question->text_question !!}
                    </div>
                </div>
            </div>
        </div>


        <div class=" " id="preview_div"></div>
   
    </div>
</div>
<hr>

<!-- Prism JS script to beautify the HTML code -->
<script type="text/javascript" src="{{ asset('samer/prism.js') }}"></script>

<!-- WIRIS script -->
<script type="text/javascript" src="{{ asset('samer/wirislib.js') }}"></script>

<!-- Google Analytics -->

<script>
    if(typeof urlParams !== 'undefined') {
        var selectLang = document.getElementById('lang_select');
        selectLang.value = urlParams[1];
    }
</script>

</body>

</html>
