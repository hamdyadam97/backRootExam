<?php

use App\Http\Resources\Exam\ExamHistory;
use App\Http\Resources\Exam\QuestionResource;
use App\Models\Userexams;
use App\User;
use Illuminate\Support\Carbon;
use App\Models\PurchaseEnquiry;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Http;
use Barryvdh\Snappy;
function pr($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}
function cacheclear()
{
    return time();
}
function getDateFormateView($date)
{
    return Carbon::parse($date)->format('d.m.Y');
}
function getLogoUrl()
{
    return asset('frontend/img/logo.png');
}
function addPageJsLink($link)
{
    return asset('assets/js/pages') . "/" . $link . '?' . time();
}

function random_strings($length_of_string) {

    return substr(md5(time()), 0, $length_of_string);
}
function getUserPassStatus($examscore,$userscore)
{
    if(!$userscore)
        $userscore = 0;
    if(!$examscore)
        $examscore = 0;

    $examscore = ($examscore) ? $examscore : 0;
    $halfexamscore = $examscore/2;

    if($userscore >= $halfexamscore)
        return true;
    else
        return false;
}

function sendWebNotification($deviceIds,$title,$body)
{
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = $deviceIds;

        $serverKey = env('FIREBASE_FCM_KEY');

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
        \Illuminate\Support\Facades\Log::info($result);

        return $result;
}

function sendsms($mobile,$message)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.sms.to/sms/send?api_key='.env('SMS_TO_API_KEY').'&bypass_optout=true&to=%2B'.$mobile.'&message='.urlencode($message),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;

}
function sendsmsReleans($mobile,$message)
{

    $curl = curl_init();

    $from = "RootsExams";
    $to = $mobile;

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.releans.com/v2/message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "sender=$from&mobile=$to&content=$message",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJpZCI6IjhkYmNkOTQ0LWI4YTctNDU0ZS1hZmVkLTVlM2RmNGUwNzk0NCIsImlhdCI6MTczMzMyNzE2NCwiaXNzIjoyMTAxOX0.84FjXULiL0ylYn2AwzavL45N-4Zc2SzSxgDVPq9SqwI"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;

}
function sendOtpThroughWhatsapp($mobile, $message){
    $sendMessageApiUrl = "https://hisocial.in/api/send";
    $apiResponse = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post($sendMessageApiUrl, [
        'number' => $mobile,
        'type' => 'text',
        'message' => $message,
        'instance_id'=> config('app.whatsapp_instance_id'),
        'access_token'=> config('app.whatsapp_access_token'),
    ]);
    $responseArray = $apiResponse->json();
    return $responseArray;
}


function getExamPdf($user_id,$exam_id,$trial_id,$new=false){
    if($new){
        unlink(public_path('/uploads/pdf/exam-' . $trial_id.'.pdf'));
    }else{
        $ifFileExist= file_exists(public_path('/uploads/pdf/exam-' . $trial_id.'.pdf') );
        if($ifFileExist){
//            echo '<a target="_blank" href="'.url('/uploads/pdf/exam-'.$trial_id.'.pdf').'">'.url('/uploads/pdf/exam-'.$trial_id.'.pdf').'</a>';
            return url('/uploads/pdf/exam-'.$trial_id.'.pdf');
        }
    }

    $query = Userexams::select('*','user_exams.score as user_exams_score','exams.score as exam_score','user_exams.id as id')
        ->join('exams','exams.id','=','user_exams.exam_id')
        ->where('user_id',$user_id)
        ->orderBy('exams.order','asc')
        ->groupBy('user_exams.id');



    $query = $query->where('exams.id',$exam_id);
    $query->where('user_exams.id',$trial_id);


    $user_exams = $query->get();
    $array = [];
    foreach ($user_exams as $key => $value) {
        $obj = new ExamHistory($value);
        $obj_array = $obj->toArray(true);

        $array[$key] = $obj_array;
        $array[$key]['trial_id'] = $value->id;
        $array[$key]['user_exam_status'] = $value->status;
        $array[$key]['user_exam_status_label'] = Userexams::$status[$value->status];
        $array[$key]['user_exams_score'] = $value->user_exams_score;
        $array[$key]['exam_score'] = $value->exam_score;
        $array[$key]['total_exam_questions'] = 0;
        $array[$key]['isPass'] = getUserPassStatus($array[$key]['exam_score'],$array[$key]['user_exams_score']);

        $exam_questions = DB::select('select `questions`.*, `user_exam_questions_answers`.`answer` as `user_selected_answer`,`exam_section`.`name` as section_title from `exam_questions`
                    inner join `questions` on `questions`.`id` = `exam_questions`.`question_id`
                    inner join `exam_section` on `exam_section`.`id` = `questions`.`section_id`
                    left join `user_exam_questions_answers` on `user_exam_questions_answers`.`exam_id` = `exam_questions`.`exam_id` and `user_exam_questions_answers`.`question_id` = questions.id and `user_exam_questions_answers`.`user_exam_id` = ? and `user_exam_questions_answers`.`user_id` = ? where questions.status=1 and questions.deleted_at IS NULL and `exam_questions`.`exam_id` = ? group by `exam_questions`.`question_id` order by exam_questions.exam_question_sort_order asc', [$value->id,$user_id,$value->exam_id]);

        // print_r($exam_questions); die;

        $exam_questions_data_tmp = [];
        foreach($exam_questions as $question){
            $question->exam_id = $value->exam_id;
            $exam_questions_data_tmp[$question->section_id][] = collect(new QuestionResource($question));
        }
        $exam_questions_data = [];
        $check = array();
        $index = 0;
        $count = 0;
        foreach($exam_questions as $question){
            if(!in_array($question->section_id,$check)){
                $check[] = $question->section_id;
                $exam_questions_data[$index]['section_id'] = $question->section_id;
                $exam_questions_data[$index]['section_title'] = $question->section_title;
                if(isset($exam_questions_data_tmp[$question->section_id]))
                    $exam_questions_data[$index]['question_list'] = $exam_questions_data_tmp[$question->section_id];
                else
                    $exam_questions_data[$index]['question_list'] = [];

                $index++;
                $count += count($exam_questions_data_tmp[$question->section_id]);
            }
        }
        $array[$key]['section_list'] = $exam_questions_data;
        $array[$key]['total_exam_questions'] = $count;

    }
    if(count($array)==0){
        return '';
    }
    $exam=$array[0];


    $arr=[];
    foreach($exam['section_list'] as  $object){
        $question_list=[];
        foreach($object['question_list'] as  $key=>$object2){

            $answers=[];
            foreach($object2['answer_options'] as $answer_key=> $answer_options){

                $answer=(object)[
                    'option_id'=>$answer_options['option_id'],
                    'option_name'=>$answer_options['option_name']
                ];
                $answers[]=$answer;
            }

            $question=(object)[
                'id'=>$object2['id'],
                'text_question'=>$object2['text_question'],
                'correct_answer_ids'=>$object2['correct_answer_ids'],
                'user_selected_answer'=>$object2['user_selected_answer'],
                'is_correct'=>$object2['is_correct'],
                'question_image'=>str_replace('http://exammanagement.test','https://admin.qodoraty.com',$object2['question_image']),
                'answer_options'=>$answers,
            ];
            $question_list[]=$question;
        }
        $section_list=(object)[
            'section_title'=>$object['section_title'],
            'question_list'=>$question_list
        ];
        $arr[]=$section_list;
    }
    $exam=$arr;

    $ifFileExist= file_exists(public_path('/uploads/pdf/exam-' . $trial_id.'.pdf') );
    if($ifFileExist){
        echo '<a target="_blank" href="'.url('/uploads/pdf/exam-'.$trial_id.'.pdf').'">'.url('/uploads/pdf/exam-'.$trial_id.'.pdf').'</a>';
        return '';
    }
    $htmlContent = view('examResult',compact('exam'))->render();
    $footer=view('footer')->render();
//        wkhtmltopdf --footer-html footer.html --debug-javascript --disable-smart-shrinking  --log-level debug index.html output.pdf
    $pdf = SnappyPdf::
    loadHTML($htmlContent);
    $pdf->setOption('footer-html', $footer);
    $pdf->setOption('header-html', $footer);
    $pdf->setOption('javascript-delay', 5000);
    $pdf->setOption('encoding', 'UTF-8');
    $pdf->setOption('no-pdf-compression', true);
    $pdf->setOption('enable-smart-shrinking', true);
//            $pdf->setOption('footer-center', 'Page [page] of [toPage]');
    $pdf->setOption('footer-center', '[page]');
    $pdf->setOption('image-quality', '80');
    $pdf->setOption('image-dpi', '60');

    $name='uploads/pdf/exam-'.$trial_id.'.pdf';
    $pdf->save($name);
//        echo '<a target="_blank" href="'.url('/'.$name).'">'.url('/'.$name).'</a>';
    return url('/'.$name);
}
