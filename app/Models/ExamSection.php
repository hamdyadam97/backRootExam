<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSection extends Model
{
    use HasFactory;

    protected $table = 'exam_section';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function questions()
    {
        return $this->belongsToMany(Questions::class, 'exam_sections' , 'section_id', 'question_id' );
    }

//    public function subcategory()
//    {
//        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
//    }

    public function scopeFilter($q)
    {
        $request = request();
        $search = $request->get('search');
        if (isset($search) && !empty($search)) {
            $q->where('name', 'LIKE', "%$search%");
        }

        if (!empty($request->category_id)){
            $q->where('category_id',$request->category_id);
        }
    }


    public function examTrails()
    {
        return $this->belongsToMany(ExamTrail::class, 'exam_trails_sections', 'section_id', 'exam_trail_id');
    }

}
