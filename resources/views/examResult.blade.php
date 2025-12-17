<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html dir="rtl">
<head>
    <meta charset="utf-8">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            font-size: 16px;
            font-family: Noto Sans Thai,sans-serif!important;

        }





        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 10px;
        }


        .badge{
            width: 17px;
            height: 17px;
            text-align: center;
            background: #125991!important;
            color: #fff;
            display: inline-block;
            border-radius: 50%;
            font-size: 13px;
            padding: 3px;
            line-height: 1.3;
            /*position: absolute;*/
            /*right: 0;*/
            /*top:5px*/
        }

        .answer-con{
            position: absolute;
            text-align: center;
            width: 4%;
            height: 100%;
            top: 18px;
            align-items: center;
            left: 5px;
        }
        .answer-bullet{
            width: 15px;
            height: 15px;
            background: #e4e7e3;
            border-radius: 50%;
            display: inline-block;

        }
        .answer p{
            padding: 0;
            margin: 0;
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
            /*overflow-x: hidden; text-overflow: ellipsis;*/
            /*height: 200px;*/
            /*word-wrap:break-word;*/

        }
        td.badge-con{
            vertical-align: top;
        }
        td p {
            /*unicode-bidi: bidi-override;*/
        }
        table{
            width: 70%;
        }
        .question-con{
            top: 11px;
            left: 8px;
        }
        .border-correct{
            border-color: #30b383!important;
        }
        .border-wrong{
            border-color: #f46a6a!important;
        }
    </style>


</head>

<body >



    @foreach($exam as  $object)
        <table  style=";border-spacing: 0 15px;" >
            <tr class="">
                <td colspan="2">
                    <div class="title-container bg-success">
                        <div class="title">
                            <span style="padding: 5px;background: #fff">{{@$object->section_title}}</span>
                            <hr style="position: relative;bottom: 24px;z-index: -1">
                        </div>
                    </div>
                </td>
            </tr>


        </table>
        @foreach($object->question_list as  $key=>$object2)
            <table  style=";border-spacing: 0 15px; page-break-inside: avoid" >
                <tr class="question " style="position: relative">
                       @php $key++ @endphp
                       <td style="width: 25px" class="badge-con">
                           <div class="badge">{{$key}}</div>
                       </td>
                       <td {{$object2->id}}  style="position: relative">


                          <div style="width: 95%;">
                              {!! @$object2->text_question !!}
                          </div>
                           <div class="question-con" style="position: absolute;">
                               @if( $object2->is_correct )
                                   <span  class="answer-bullet answer-correct"></span>
                               @endif
                               @if($object2->is_correct==0 )
                                   <span  class="answer-bullet answer-wrong"></span>
                               @endif
                           </div>
                       </td>



                </tr>
                @if($object2->question_image!="")
                    <tr>
                        <td colspan="2">
                            <div>
                                <img src="{{$object2->question_image}}"  style="max-width: 100%" alt="">
                            </div>
                        </td>
                    </tr>
                @endif
                @foreach(@$object2->answer_options as $answer_key=> $answer_options)

                    <tr class="tr">
                        @php
                            $correct='';
                            $wrong='';
                            if(($answer_options->option_id == $object2->correct_answer_ids)  ){
                                $correct='<span  class="answer-bullet answer-correct"></span>';
                            }
                           elseif(($answer_options->option_id == $object2->user_selected_answer) && !$object2->is_correct){
                                $wrong='<span  class="answer-bullet answer-wrong"></span>';
                           }

                        @endphp
                        <td class="answer {{$correct!=''?'border-correct':''}} {{$wrong!=''?'border-wrong':''}}" colspan="2" style=";position: relative;border:1px solid #e4e7e3 ;padding: 10px;border-radius: 5px;">
                            <div {{$answer_options->option_id}} style="width: 95%;display: inline-block">
                                {!! $answer_options->option_name !!}
                            </div>

                            <div class="answer-con " style="width: 4%">

                               {!! $correct !!}
                               {!! $wrong !!}

                            </div>

                        </td>
                    </tr>


                @endforeach
            </table>

        @endforeach

    @endforeach




</body>
</html>
