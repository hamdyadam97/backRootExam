<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSubCategory extends Model
{
    use SoftDeletes;

    protected $table = 'sub_sub_categories';

    protected $guarded = [];

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_cat_id');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class, 'sub_subcategory_id');
    }
    public function scopeFilter($q)
    {
        $request = request();
        $search = $request->get('search');
        if (isset($search) && !empty($search)) {
            $q->where(function ($w) use ($search) {
                $w->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%")
                    ->orWhere('order', 'LIKE', "%$search%");
            });
        }

        if (!empty($request->category_id)) {
            $q->whereHas('subCategory' ,function ($qq){
                $qq->filter();
            });
        }

        if (!empty($request->sub_category_id)) {
            $q->where('sub_cat_id', $request->sub_category_id);
        }
    }


    public function examTrails()
    {
        return $this->belongsToMany(ExamTrail::class, 'sub_sub_categories_exam_trails', 'sub_sub_category_id', 'exam_trail_id');
    }
}
