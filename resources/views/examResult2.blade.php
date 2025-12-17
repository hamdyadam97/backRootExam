<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta charset="utf-8">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title></title>
    <script type="text/x-mathjax-config">
       MathJax.Ajax.config.path["arabic"] = "https://cdn.rawgit.com/Edraak/arabic-mathjax/v1.0/dist";
     MathJax.Hub.Config({
        "CommonHTML": { scale: 200 }, // Adjust font size for CommonHTML output
        "SVG": { scale: 200 }     ,
        TeX: {extensions: ["mhchem.js"]},
        tex2jax: {
            inlineMath: [['$','$'], ['\\(','\\)']],
            displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
            processEscapes: true,
        },
        'HTML-CSS': {
            undefinedFamily: 'Noto Sans Thai,sans-serif'
        },
         extensions: [
            "[arabic]/arabic.js"
        ]
    });


</script>

    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.9/MathJax.js?config=TeX-MML-AM_CHTML">
    </script>


    <style type = "text/css">
        html, body { text-align: justify; text-justify: inter-word; text-rendering: optimizeLegibility; word-break: break-word; }
        @font-face {
            font-family: 'Noto Sans Thai';
            src: url('https://admin.qodoraty.com/Cairo-VariableFont_slnt,wght.ttf') format('truetype');
        }


        .mjx-full-width{
            width: auto!important;
        }
        /*.mjx-chtml{*/
        /*    font-size: 24px!important;*/
        /*}*/
        .MJXc-TeX-unknown-I{
            font-family:Noto Sans Thai,sans-serif!important;
        }
        @page {
            margin:30px;
            @bottom-center { content: element(footer) }
        }
        p {
            font-family: Noto Sans Thai,sans-serif!important;
        }

        body {
            text-rendering: geometricPrecision;
            padding: 0;
            font-size: 16px;
            font-family: Noto Sans Thai,sans-serif!important;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }


        header {
            margin-bottom: 20px;
            padding: 0 10px;
        }
        .col-sm-4 {
            width: 33.33333333%;
        }
        .col-sm-2 {
            width: 16.66666667%;
        }
        .col-sm-1 {
            width: 8.33333333%;
            min-height: 20px;
        }
        .text-right{
            text-align: right;
        }
        .text-left{
            text-align: left;
        }
        .text-center{
            text-align: center;
        }
        .header_logo{
            text-align: center;
        }
        .bold-text{
            font-weight: bolder;
        }
        .w-100{
            width: 100%;
        }

        .table-bordered {
            border-collapse: collapse;
            border: 2px solid #000;
        }
        .table-bordered thead td, .table-bordered thead th {
            border-bottom-width: 2px;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000;
            padding: 5px;
        }
        .d-block{
            display: block;
        }
        td.py-3{
            padding-top: 20px!important;
            padding-bottom: 20px!important;
        }
        .main-bg{
            /*background: #0eae92;*/
            background: rgba(14,174,146,.7);
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }


        .badge{
            width: 17px;
            height: 17px;
            text-align: center;
            background: #125991;
            color: #fff;
            display: inline-block;
            border-radius: 50%;
            font-size: 13px;
            padding: 3px;
            /*margin-left: 10px;*/
            /*position: absolute;*/
            /*right: 0;*/
            /*top:10px*/
        }
        .d-inline-block{
            display: inline-block;
        }
        .answer{
            width: 15px;
            height: 15px;
            background: #e4e7e3;
            border-radius: 50%;
            display: inline-block;
            vertical-align: middle;
            /*top: calc(50% - 7px);*/
            /*position: absolute;*/
        }
        .answer-correct{
            background: #30b383;
        }
        .answer-wrong{
            background: #f46a6a;
        }
        math{
            font-size: 24px;
        }
        .page-break {
            page-break-before: always;
        }
        .page-break-before {
            page-break-before: always;
        }
        .page-break-after {
            page-break-after: always;
        }
        .question p:first-child{
            padding-top: 0;
            margin-top: 0;

        }
        .question p{
            padding: 0;
            margin: 0;
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        td{
            /*height: 200px;*/
            /*word-wrap:break-word;*/
        }
        table tr {page-break-inside: avoid}
        p{
            line-height: 5;
        }
    </style>


</head>

<body dir="rtl">
<div style="  max-width: 99%;">
    @php
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
$englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    @endphp
    @foreach($exam['section_list'] as  $object)
            <table  style="width: 100%" >
                   <tbody>
                       <tr class="">
                           <td colspan="2">
                               <div class="title-container bg-success">
                                   <div class="title">
                                       <div style="padding: 5px;background: red">{{$object['section_title']}}</div>
                                       <hr style="position: relative;bottom: 24px;z-index: -1">
                                   </div>
                               </div>
                           </td>
                       </tr>

                   </tbody>

                </table>
        @foreach($object['question_list'] as  $key=>$object2)
            <table  style="width: 100%" >
                <tbody>
                    <tr class="question ">
                    @php $key++ @endphp
                    {{--                               <td >--}}
                    {{--                                   <div class="badge">{{$key}}</div>--}}
                    {{--                               </td>--}}
                    <td {{$object2['id']}} >


                        {!! $object2['text_question'] !!}

                    </td>



                </tr>
                    @foreach($object2['answer_options'] as $answer_key=> $answer_options)
                @php
                    $answer=str_replace('&nbsp;',' ',strip_tags($answer_options['option_name']));

                @endphp
                <tr class="tr{{$answer_options['option_id']}} {{$key%3}}">
                    <td  colspan="2" style=";border:1px solid #e4e7e3 ;padding: 10px;border-radius: 5px;" >
                        {!!  strip_tags($answer_options['option_name'])  !!}
                        {{--                                       <p style="width: 95%;display: inline-block"> {!!  $answer  !!}</p>--}}
                        {{--                                       <div style="display: inline-block;text-align: center ;width: 4%">--}}
                        {{--                                           <span style="" class="answer {{ $answer_options['option_id'] == $object2['user_selected_answer'] ? 'answer-correct' : '' }}"></span>--}}
                        {{--                                       </div>--}}
                    </td>
                </tr>


            @endforeach
                </tbody>
            </table>

        @endforeach

    @endforeach



</div>

</body>
</html>
