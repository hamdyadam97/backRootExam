<?php

namespace App\Http\Controllers;

use App\Exports\QuestionExport;
use App\Http\Requests\QuestionRequest;
use App\Imports\QuestionImportFile;
use App\Models\Category;
use App\Models\Examquestions;
use App\Models\Exams;
use App\Models\ExamSection;
use App\Models\ExamSections;
use App\Models\Questionanswers;
use App\Models\Questions;
use App\Models\QuestionsTopic;
use App\Models\QuestionTopic;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['questionType'] = Questions::$questionType;
        $data['exams'] = Exams::all();
        $data['categories'] = Category::query()->with(['subCategories.subCategories', 'topics', 'exam_section'])->get();
//        $data['sections'] = ExamSection::all();

        $data['session_category_id'] = session('category_id', null);
        $data['session_sub_category_id'] = session('sub_category_id', null);
        $data['session_sub_subcategory_id'] = session('sub_subcategory_id', null);
        $data['session_questions_topic_id'] = session('questions_topic_id', null);
        $data['session_section_id'] = session('section_id', null);

        if (isset($data['session_category_id'])) {
            $data['sub_categories'] = SubCategory::query()->where('cat_id', $data['session_category_id'])->get();
            if (isset($data['session_questions_topic_id'])) {
                $data['topics'] = QuestionsTopic::query()->where('category_id', $data['session_category_id'])->get();
            }
            if (isset($data['session_section_id'])) {
                $data['sections'] = ExamSection::query()->where('category_id', $data['session_category_id'])->get();
            }
        }
        if (isset($data['session_sub_category_id'])) {
            $data['sub_sub_categories'] = SubSubCategory::query()->where('sub_cat_id', $data['session_sub_category_id'])->get();
        }

        return view('question.index', $data);
    }

    public function savesort(Request $request)
    {
        foreach ($request->ids as $key => $value) {
            Examquestions::where('id', $value)->update(['exam_question_sort_order' => $key]);
        }
        echo "saved";
    }

    public function sorting(Request $request)
    {

        $data = [];
        if ($request->exam_id) {
            $query = Questions::select('questions.text_question', 'exam_questions.id')->where('questions.deleted_at', null)->where('questions.status', 1);
            $query = $query->join('exam_questions', 'exam_questions.question_id', '=', 'questions.id')->where('exam_questions.exam_id', $request->exam_id)->orderBy('exam_questions.exam_question_sort_order', 'asc');
            $data = $query->get();
        }

        $exams = Exams::all();
        return view('question.sorting', compact('data', 'exams'));
    }

    public function export()
    {
        return Excel::download(new QuestionExport(), 'download.xlsx');

    }

    public function create()
    {
        $questionType = Questions::$questionType;
        $exams = Exams::all();
        $sections = ExamSection::all();
        $questionTopics = QuestionsTopic::all();
        $categories = Category::query()->with(['subCategories.subCategories', 'topics', 'exam_section'])->get();
        return view('question.create', compact('questionType', 'exams', 'sections', 'questionTopics', 'categories'));
    }

    public function edit($id)
    {

        $questionType = Questions::$questionType;
        $exams = Exams::all();
        $sections = ExamSection::all();
        $questionTopics = QuestionsTopic::all();

        $question_answers = Questions::with(['questions_answers', 'exam_sections', 'question_topic'])->find($id);

        $exam_sections = (!empty($question_answers->exam_sections)) ? $question_answers->exam_sections->pluck('section_id')->toArray() : [];
        $question_topic = (!empty($question_answers->question_topic)) ? $question_answers->question_topic->pluck('topic_id')->toArray() : [];
        $question_answers->question_image = ($question_answers->question_image) ? asset('storage/question_images/' . $question_answers->question_image) : "";
        $question_answers->answer_image = ($question_answers->answer_image) ? asset('storage/answer_images/' . $question_answers->answer_image) : "";

        $categories = Category::query()->with(['subCategories.subCategories', 'topics', 'exam_section'])->get();

        return view('question.edit', compact('categories', 'questionType', 'exams', 'sections', 'question_answers', 'id', 'questionTopics', 'exam_sections', 'question_topic'));
    }

    public function get(Request $request)
    {

        $exam_id = $request->filter;

        $status = Questions::$status;
        $questionType = Questions::$questionType;
        $data = Questions::query()->with(['questions_answers'])->filter()->orderByDesc('questions.id');

        session()->put(['page' => getPageNumber(\Request::fullUrl())]);

        if ($exam_id) {
            $data = $data->leftJoin('questions_answers', 'questions_answers.question_id', '=', 'questions.id')->where('questions_answers.exam_id', $exam_id)->groupBy('questions.id');
        }

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? @$status[$row->status] : "";
            })
            ->editColumn('hint', function ($row) {
                return (!empty($row->show_hint) && $row->show_hint == 1) ? $row->hint : '-';
            })
            ->editColumn('correct_answers', function ($row) {
                $correct_answers = $row->questions_answers->whereIn('id', explode(',', $row->correct_answer_id))->pluck('answer_option')->toArray();

                return implode(' | ', $correct_answers);
            })
            ->editColumn('show_answer', function ($row) {
                return (!empty($row->show_answer) && $row->show_answer == 1) ? 'YES' : 'NO';
            })
            ->editColumn('question_type', function ($row) use ($questionType) {
                return isset($row->question_type) ? @$questionType[$row->question_type] : '-';
            })
            ->editColumn('answer_type', function ($row) {
                if ($row->answer_type == 1) {
                    return 'Radio';
                }
                if ($row->answer_type == 2) {
                    return 'Multiple choice';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function addupdate(QuestionRequest $request)
    {
        if ($request->ajax()) {

            // if(!empty($request->answer_option_id_bkp)){
            //     $tot_values = array_count_values($request->answer_option_id_bkp);
            //     if(!in_array('1',$request->answer_option_id_bkp)){
            //         $rules['answer_option_id']='required';
            //         $messages['answer_option_id.required']='Atleast one checkbox required to check.';
            //     }else if(in_array('1',$request->answer_option_id_bkp) && $tot_values[1] > 1 && $request->answer_type == 1){
            //         $rules['answer_option_id']=function ($attribute, $value, $fail) use($tot_values) {
            //             if($tot_values[1] > 1){
            //                 $fail('Only one checkbox you can select for answer type radio.');
            //             }
            //         };
            //     }
            // }

            $data = $request->only(Questions::FILLABLE);

            $successmsg = $request->id ? trans('Question updated successfully') : trans('Question added successfully');

            $question = null;
            if ($request->id) {
                $question = Questions::query()->where('id', $request->id)->first();
                if (!$question) {
                    $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                    return response()->json($result);
                }
            }


            if ($request->hasFile('question_image') && $request->question_image) {
                $dir = "question_images/";

                if (@$question->question_image) {
                    if (Storage::disk('local')->exists($dir . $question->question_image)) {
                        Storage::delete($dir . $question->question_image);
                    }
                }
                $extension = $request->file("question_image")->getClientOriginalExtension();
                $filename = uniqid() . "_" . time() . "." . $extension;
                Storage::disk("local")->put($dir . $filename, \File::get($request->file("question_image")));

                $data['question_image'] = $filename;
                // $question->question_image = $filename;
            }
            if ($request->hasFile('answer_image') && $request->answer_image) {
                $dir = "answer_images/";

                if (@$question->answer_image) {
                    if (Storage::disk('local')->exists($dir . $question->answer_image)) {
                        Storage::delete($dir . $question->answer_image);
                    }
                }
                $extension = $request->file("answer_image")->getClientOriginalExtension();
                $filename = uniqid() . "_" . time() . "." . $extension;
                Storage::disk("local")->put($dir . $filename, \File::get($request->file("answer_image")));

                $data['answer_image'] = $filename;
                // $question->answer_image = $filename;
            }

            $checkbox_id = [];
            // $checkbox_ids_str = "";
            if (!empty($request->answer_option_id_bkp)) {
                foreach ($request->answer_option_id_bkp as $key => $value) {
                    if ($value == 1) {
                        $checkbox_id[] = $key + 1;
                    }
                }
                // $checkbox_ids_str = implode($checkbox_id, ',');
            }
            // $question->correct_answer_id = $checkbox_ids_str;
            $data['status'] = ($request->status == 1) ? 1 : 0;

            $question = Questions::query()->updateOrCreate([
                'id' => $request->id,
            ], $data);

            if ($question) {

                $check = Examquestions::where('question_id', $question->id)->where('exam_id', 1)->first();

                $examQuestion = new Examquestions([
                    'exam_id' => 1,
                    'question_id' => $question->id,
                ]);
                if (!$check) {
                    $examQuestion->save();
                }

                $question->exam_sections()->delete();
                if (isset($request->section_id) && is_array($request->section_id)) {
                    foreach ($request->section_id as $section_id) {
                        if (!$section_id) {
                            continue;
                        }
                        ExamSections::query()->create([
                            'question_id' => $question->id,
                            'section_id' => $section_id,
                        ]);
                    }
                }


                /* Code For Questions Topic */
                $question->question_topic()->delete();
                if (isset($request->questions_topic_id) && is_array($request->questions_topic_id)) {
                    foreach ($request->questions_topic_id as $questions_topic_id) {
                        QuestionTopic::query()->create([
                            'question_id' => $question->id,
                            'topic_id' => $questions_topic_id,
                        ]);
                    }
                }

                $correct_answer_ids = [];
                $count = 1;
                foreach ($request->correct_answer_editor as $key => $answer_option) {
                    $answer_opt_id = (isset($request->answer_opt_ids[$key])) ? $request->answer_opt_ids[$key] : null;
                    if (!$answer_opt_id) {
                        $question_answer = new Questionanswers;
                    } else {
                        $question_answer = Questionanswers::where('id', $answer_opt_id)->first();
                    }
                    $question_answer->exam_id = 1;
                    $question_answer->question_id = $question->id;
                    $question_answer->answer_option = $answer_option;
                    if (!$question_answer->save()) {
                        $result = ['status' => false, 'message' => 'something went wrong while saving answer option data.', 'data' => []];
                    }
                    if (in_array($count, $checkbox_id)) {
                        $correct_answer_ids[] = $question_answer->id;
                    }
                    $count++;
                }
                $question->correct_answer_id = implode(",", $correct_answer_ids);
                $question->save();
                $result = ['status' => true, 'message' => $successmsg, 'data' => []];
            } else {
                $result = ['status' => false, 'message' => 'something went wrong while saving question data.', 'data' => []];
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $question_answers = Questions::with('questions_answers')->find($request->id);
            $question_answers->question_image = ($question_answers->question_image) ? asset('storage/question_images/' . $question_answers->question_image) : "";
            $question_answers->answer_image = ($question_answers->answer_image) ? asset('storage/answer_images/' . $question_answers->answer_image) : "";
            $result = ['status' => true, 'message' => '', 'data' => $question_answers];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        if (!empty($request->id)) {
            $question = Questions::where('id', $request->id)->first();
            $question_answers = Questionanswers::where('question_id', $request->id)->get();
            $exam_questions = Examquestions::where('question_id', $request->id)->get();

            if ($question && $question->delete() && $question_answers->each->delete() && $exam_questions->each->delete()) {
                $result = ['status' => true, 'message' => trans('Delete successfully')];
            } else {
                $result = ['status' => false, 'message' => 'Record Deletion failed'];
            }
            return response()->json($result);
        }
    }

    public function import()
    {
        return view('question.import');
    }

    public function importFile(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv'
        ]);

        $file = $request->file('csv_file');
        $extension = $file->getClientOriginalExtension();
        if ($extension == "csv") {

            Excel::import(new QuestionImportFile(), request()->file('csv_file'));
            session()->forget(['page', 'category_id', 'sub_category_id', 'sub_subcategory_id', 'questions_topic_id', 'section_id']);

            return redirect(route('question'))->withSuccess('Successfully data import');
        } else {
            return redirect()->back()->withError('Only .csv file is allowed');
        }
    }
}
