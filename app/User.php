<?php

namespace App;

use App\Models\Category;
use App\Models\ExamTrail;
use App\Models\Packges;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\Userpackges;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    use SoftDeletes;

    const ACTIVE = 1;
    const INACTIVE = 0;

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public static $role = [
        1 => 'Admin',
        2 => 'User',
    ];

    protected $with = ['activeSubscriptions'];

    public function isAdmin()
    {
        return ($this->role_type == 1);
    }

    public function isUser()
    {
        return ($this->role_type == 2);
    }


    public function subscriptions()
    {
        return $this->hasMany(Userpackges::class);
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('subscription_status' , 1);
    }

    public function categories_count()
    {
        return Category::query()
            ->whereHas('packages.subscriptions', function ($query) {
                $query->where('user_id', $this->id)->where('subscription_status' , 1);  // Filter by the current user
            })
            ->distinct()
            ->count();
    }

    public function categories()
    {
        return Category::query()
            ->whereHas('packages.subscriptions', function ($query) {
                $query->where('user_id', $this->id)->where('subscription_status' , 1);  // Filter by the current user
            })
            ->distinct()
            ->get();
    }

    public function subcategoriesCount()
    {

        $subscriptions = $this->activeSubscriptions()
            ->with(['getPackage.subCategories', 'getPackage.subCategories.questions'])
            ->get();

        $subscriptions->map(function ($sub) {
            $subs = $sub->getPackage?->subCategories ?? collect([]);
            if (!count($subs)) {
                $cat = $sub?->getPackage?->category;
                if ($cat) {
                    $subs = $cat->subCategories ?? collect([]);
                }
            }
            return $subs->filter()->unique();
        });

        return $subscriptions->count();
    }

    public function subcategories()
    {
        $subscriptions = $this->activeSubscriptions()
            ->with(['getPackage.subCategories', 'getPackage.subCategories.questions'])
            ->get();

        $result = collect([]);
        $subscriptions->map(function ($sub) use ($result) {
            $subs = $sub->getPackage?->subCategories ?? collect([]);
            if (!count($subs)) {
                $cat = $sub?->getPackage?->category;
                if ($cat) {
                    $subs = $cat->subCategories ?? collect([]);
                }
            }

            foreach ($subs as $sub_cat) {
                $result->push(@$sub_cat);
            }
        });
        return $result->filter()->unique();
    }

    public function subSubcategories()
    {

        $subsubcategory = $this->subcategories()->pluck('id')->toArray();

        return SubSubCategory::query()->whereIn('sub_cat_id', $subsubcategory)->withCount('questions')->get();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'thumb',
        'role_type',
        'mobile',
        'token',
        'device_id',
        'score',
        'status',
        'email',
        'password',
        'email_verified_at',
        'mobile_verified_at',
        'otp',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'mobile_country_code', 'dial_code', 'mobile_number',
        'specialization',
        'governorate',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
            'birth_date' => 'date',

    ];

    public function userExams()
    {
        return $this->hasMany(Models\UserExams::class);
    }


    public function examtrails()
    {
        return $this->hasMany(ExamTrail::class);
    }





    public function makeTrailSubscription()
    {
        $package = Packges::query()->where('is_trial', 1)->first();
        if ($package) {
            $has_trial_before = Userpackges::query()->where('user_id', $this->id)
                ->where('package_id', $package->id)->first();

            if (!$has_trial_before) {
                Userpackges::query()->create([
                    'user_id' => $this->id,
                    'package_id' => $package->id,
                    'start_date' => now(),
                    'end_date' => now()->addDays($package->period),
                ]);
            }
        }
    }

    public function scopeFilter($q)
    {
        $request = request();
        if ($request->filled('status') && in_array($request->status, [0, 1])) {
            $q->where('status', $request->status);
        }
        if ($request->filled('first_name') && !empty($request->first_name)) {
            $q->where('first_name', 'like', "%" . $request->first_name . '%');
        }
        if ($request->filled('last_name') && !empty($request->last_name)) {
            $q->where('last_name', 'like', "%" . $request->last_name . '%');
        }
        if ($request->filled('mobile') && !empty($request->mobile)) {
            $mobile = $request->mobile[0] == "0" ? ltrim($request->mobile, '0') : $request->mobile;
            $q->where('mobile', 'like', "%" . $mobile . '%');
        }
    }
}
