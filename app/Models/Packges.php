<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packges extends Model
{
    use SoftDeletes;

    protected $table = 'packages';

    const FILLABLE = [
        'name', 'price', 'status', 'icon', 'period', 'category_id',
        'created_at', 'updated_at', 'deleted_at',
    ];
    protected $fillable = self::FILLABLE;

//'number_of_questions',

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'packages_sub_categories', 'package_id');
    }

    public function scopeFilter($q)
    {
        $request = request();
        $search = $request->get('search');
        if (isset($search)) {
            $q->where(function ($w) use ($search) {
                $w->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('price', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%");
//                    ->orWhere('number_of_questions', 'LIKE', "%$search%");
            });
        }

        if ($request->has('categories') && is_array($request->get('categories')) && count($request->get('categories'))) {
            $q->whereIn('category_id', $request->get('categories'));
        }
        if ($request->has('categories') && !is_array($request->get('categories'))) {
            $q->where('category_id', $request->get('categories'));
        }

        if ($request->has('sub_categories') && is_array($request->get('sub_categories')) && count($request->get('sub_categories'))) {
            $q->whereHas('subCategories', function ($qq) use ($request) {
                $qq->whereIn('id', $request->get('sub_categories'));
            });
        }
    }

    public function subscriptions()
    {
        return $this->hasMany(Userpackges::class , 'package_id');
    }
}
