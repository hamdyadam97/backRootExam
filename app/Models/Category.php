<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name', 'icon', 'order', 'status', 'created_at', 'updated_at', 'deleted_at',
    ];

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'cat_id');
    }

    public function topics()
    {
        return $this->hasMany(QuestionsTopic::class, 'category_id');
    }

    public function exam_section()
    {
        return $this->hasMany(ExamSection::class, 'category_id');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class, 'category_id');
    }

    public function scopeActive($q)
    {
        $q->where('status', 1);
    }

    public function scopeFilter($q)
    {
        $request = request();
        $search = $request->get('search');
        if (isset($search) && !empty($search)) {
            $q->where('name', 'LIKE', "%$search%");
        }
    }

    public function packages()
    {
        return $this->hasMany(Packges::class, 'category_id', 'id');
    }

    public function examTrails()
    {
        return $this->belongsToMany(ExamTrail::class, 'categories_exam_trails', 'category_id', 'exam_trail_id');
    }
}
