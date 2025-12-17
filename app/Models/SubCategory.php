<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use SoftDeletes;

    protected $table = 'sub_categories';

    protected $fillable = [
        'cat_id', 'name', 'icon', 'order', 'status', 'created_at', 'updated_at', 'deleted_at',
    ];

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }

    public function subCategories()
    {
        return $this->hasMany(SubSubCategory::class, 'sub_cat_id');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class, 'sub_category_id');
    }

//    public function topics()
//    {
//        return $this->hasMany(QuestionsTopic::class, 'sub_category_id');
//    }
//
//    public function exam_section()
//    {
//        return $this->hasMany(ExamSection::class, 'sub_category_id');
//    }

    public function scopeActive($q)
    {
        $q->where('status', 1);
    }

    public function scopeFilter($q)
    {
        $request = request();
        $search = $request->get('search');
        if (isset($search) && !empty($search)) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%")
                    ->orWhere('order', 'LIKE', "%$search%");
            });
        }

        if (!empty($request->category_id)) {
            $q->where('cat_id', $request->category_id);
        }
    }

    public function package()
    {
        return $this->belongsToMany(Packges::class, 'packages_sub_categories', 'sub_category_id', 'package_id');
    }

    public function examTrails()
    {
        return $this->belongsToMany(ExamTrail::class, 'sub_categories_exam_trails', 'sub_category_id', 'exam_trail_id');
    }

}
