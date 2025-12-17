<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title', 'description', 'category_id', 'image', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($q)
    {
        $category_id = request('category_id');
        $search = request()->get('search');

        if (isset($category_id) && !empty($category_id)){
            $q->where('category_id' , $category_id);
        }
        if (isset($search) && !empty($search)){
            $q->orWhere('title', 'LIKE', "%$search%");
        }
    }
}
