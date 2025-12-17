<?php

namespace App\Http\Controllers;

use App\Helpers\HyperPay;
use App\Helpers\HyperPayWeb;
use App\Http\Resources\Exam\ExamHistory;
use App\Http\Resources\Exam\QuestionResource;
use App\Models\MoneyLogs;
use App\Models\Userexams;
use App\User;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Examquestions;
use App\Models\SubCategory;
use App\Models\Exams;
use Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SnappyPdf;
use Barryvdh\Snappy\Facades\SnappyImage as Snappy;
use Spatie\Browsershot\Browsershot;

class TestController extends Controller
{
    public function __construct()
    {
//         $this->middleware('auth');
    }
    public function index(){
        $res=array (
            'code' => 1,
            'values' =>
                (object) array(
                    'id' => '8ac7a49f8e1c08c6018e2019e8235295',
                    'paymentType' => 'DB',
                    'paymentBrand' => 'VISA',
                    'amount' => '100.00',
                    'currency' => 'JOD',
                    'descriptor' => '0259.4879.4356 Qodoraty',
                    'merchantTransactionId' => '#411jIrc',
                    'result' =>
                        (object) array(
                            'code' => '000.100.112',
                            'description' => 'Request successfully processed in \'Merchant in Connector Test Mode\'',
                        ),
                    'resultDetails' =>
                        (object) array(
                            'ConnectorTxID1' => '8ac7a49f8e1c08c6018e2019e8235295',
                            'connectorId' => '8ac7a49f8e1c08c6018e2019e8235295',
                            'CardholderInitiatedTransactionID' => '123456789012345',
                            'response.acquirerCode' => '00',
                            '3DSecure.acsEci' => '05',
                            'clearingInstituteName' => 'SAIB MPGS',
                            'authorizationResponse.stan' => '77313',
                            'transaction.receipt' => '406822077313',
                            'merchantCategoryCode' => '5999',
                            'transaction.acquirer.settlementDate' => '2024-03-08',
                            'reconciliationId' => '0259.4879.4356',
                            'transaction.authorizationCode' => '077313',
                            'sourceOfFunds.provided.card.issuer' => 'JPMORGAN CHASE BANK, N.A.',
                            'response.acquirerMessage' => 'Approved',
                        ),
                    'card' =>
                        (object) array(
                            'bin' => '411111',
                            'binCountry' => 'PL',
                            'last4Digits' => '1111',
                            'holder' => 'rashhhhhh',
                            'expiryMonth' => '06',
                            'expiryYear' => '2026',
                            'issuer' =>
                                (object) array(
                                    'bank' => 'CONOTOXIA SP. Z O.O',
                                ),
                            'type' => 'DEBIT',
                            'level' => 'CLASSIC',
                            'country' => 'PL',
                            'maxPanLength' => '16',
                            'binType' => 'PERSONAL',
                            'regulatedFlag' => 'N',
                        ),
                    'customer' =>
                        (object) array(
                            'email' => '@qodoraty.com',
                            'ip' => '45.98.73.67',
                            'ipCountry' => 'EG',
                        ),
                    'customParameters' =>
                        (object) array(
                            'SHOPPER_MSDKIntegrationType' => 'Checkout UI',
                            'SHOPPER_device' => 'Google google Pixel 5',
                            'CTPE_DESCRIPTOR_TEMPLATE' => '',
                            'SHOPPER_OS' => 'Android 13',
                            'SHOPPER_MSDKVersion' => '4.12.0',
                        ),
                    'risk' =>
                        (object) array(
                            'score' => '100',
                        ),
                    'buildNumber' => '4cf0da2a0da510e18c9a3ee629818eeb6bd84fff@2024-03-07 15:31:01 +0000',
                    'timestamp' => '2024-03-08 22:05:59+0000',
                    'ndc' => 'DFF4FFE73748FD5B3B5A1484E774AC39.uat01-vm-tx03',
                ),
        );
        return $res['values']->merchantTransactionId;
        $user=User::find(47);
        $id='8CA4F7F6792D2F480ACA834CFB6D6DA3.uat01-vm-tx04';
        $money_log=MoneyLogs::where('payment_id',$id)->first();




        $url = env('HYPERPAY_URL', '') . "/v1/checkouts/" . $id . "/registration";
        $url .= "?entityId=" . env('HYPERPAY_ENTITY_ID', '');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN', '')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       return $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return 'a';
        dd($responseData);
      return  $values = json_decode($responseData);



        $url = env('HYPERPAY_URL');
        $url .= '/v1/checkouts/' . $id . '/payment';
        $url .= "?entityId=" . ((strtolower($payment_type) === self::VISA) ? env('HYPERPAY_ENTITY_ID') : env('HYPERPAY_MADA_ENTITY_ID'));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !env('HYPERPAY_TEST'));// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $values = json_decode($responseData);
        return $values;
        return $response = HyperPayWeb::validateCheckout($id, 'VISA',true);

        return $response = HyperPay::validateCheckoutRegistration($user , $money_log , $id );

//        $pdfContent = Browsershot::html('<h1>Hello world!!</h1>')
//            ->setNodeBinary('/D:\laragon\bin\nodejs\node-v14\node.exe')
//            ->pdf();
//        return $pdfContent;

        $user = User::find(\request()->user_id?\request()->user_id:23);
        $user_id = $user->id;
        $exam_id = \request()->user_id?\request()->exam_id:15;
        $trial_id = \request()->trial_id?\request()->trial_id:196;
        $query = Userexams::select('*','user_exams.score as user_exams_score','exams.score as exam_score','user_exams.id as id')
            ->join('exams','exams.id','=','user_exams.exam_id')
            ->where('user_id',$user->id)
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
                    left join `user_exam_questions_answers` on `user_exam_questions_answers`.`exam_id` = `exam_questions`.`exam_id` and `user_exam_questions_answers`.`question_id` = questions.id and `user_exam_questions_answers`.`user_exam_id` = ? and `user_exam_questions_answers`.`user_id` = ? where questions.status=1 and questions.deleted_at IS NULL and `exam_questions`.`exam_id` = ? group by `exam_questions`.`question_id` order by exam_questions.exam_question_sort_order asc', [$value->id,$user->id,$value->exam_id]);

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
        if(\request()->obj){
         return $exam;
        }
        if(\request()->pdf){
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
             echo '<a target="_blank" href="'.url('/'.$name).'">'.url('/'.$name).'</a>';
             return true;
        }
        return view('examResult',compact('exam'));
    }
}

