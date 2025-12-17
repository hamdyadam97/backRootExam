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

<script>window.MathJax = { MathML: { extensions: ["mml3.js", "content-mathml.js"]}};</script>
<script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=MML_HTMLorMML"></script>

@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group-card-header d-flex align-items-center mb-4">
                    <div class="col-md-4 mt-3">
                        <div class="mb-3">
                            {{-- <label for="Text" class="form-label">Exam</label> --}}
                            <select name="exam_filter" id="exam_filter" class="form-control select2 exam_id" placeholder="@lang('Select Exam')">
                                <option value="">Select Exam</option>
                                @foreach ($exams as $exam)
                                    <option @if(request()->exam_id==$exam->id) selected @endif value="{{ $exam->id }}">{{ $exam->title }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback" id="exam_idError" data-ajax-feedback="text_question"role="alert"></span>
                        </div>
                    </div>
                </div>
                @if($data)
                <button type="button" class="btn btn-success save-order">Save</button>

                <div id="example1">
                    @foreach($data as $i=>$list)
                    <div class="list-group-item sort-item" data-id="{{$list->id}}" style="width: 100%;    border: 1px solid #d4a9a9;padding: 10px;margin-top: 2px;">
                        <div style="width:100%; text-align: right;"><b>{!! $list->text_question !!}</b></div>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('/')}}assets/js/Sortable.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<script type="text/javascript">
var url = "{{route('question.sorting')}}";
var sortUrl = "{{route('question.savesort')}}";
$('.exam_id').change(function(){
    location.href = url + "?exam_id="+$(this).val();
})
if($('#example1').length>0)
{
    var isChanged = 0;
    var sortable  = Sortable.create(example1, {
      animation: 150,
      ghostClass: 'blue-background-class',
      sort: true,
      onUpdate: function (/**Event*/evt) {
        var reconnect = false;
        window.onbeforeunload = function () {
          var msg = "Are you sure you want to leave?";
          reconnect = true;
          return msg;
        }; 
        isChanged=1; 
      },
    }); 


    $('body').on('click','.save-order',function(){
            arr = [];
            $('.sort-item').each(function() {
                arr.push($(this).attr('data-id'))
            });
            window.onbeforeunload = null;
            isChanged=0;

            var $this = $(this);
            $.ajax({
                url: sortUrl,
                type: 'POST',
                data: {'ids':arr},
                success: function(result) {
                    // alert("Saved");    
                    showMessage("success", "Saved");                
                },
                error: function(error) {
                    $($this).find('button[type="submit"]').prop('disabled', false);
                    alert('Something went wrong!', 'error');
                    // location.reload();
                }
            });
        });

}
</script>
@endsection


