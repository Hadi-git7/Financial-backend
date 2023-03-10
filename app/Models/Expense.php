<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'type',
        'title',
        'description',
        'amount',
        'currency',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'category_id',
        'category_title',
        'admin_id',
        'start_date',
        'end_date',
    ];

    // protected $dates = [
    //     'start_date',
    //     'end_date',
        
    // ];


protected $appends=['category_title'];



public function categoryTitle(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'title');
    }
   

    public function getCategoryTitleAttribute()
    {
        return $this->category->title; // use the correct attribute name here
    }


    public function createdBy()
{
    return $this->belongsTo(Admin::class, 'username');
}

    public function updatedBy()
{
    return $this->belongsTo(Admin::class, 'username');
}

public function deletedBy()
{
    return $this->belongsTo(Admin::class, 'username');
}


    // public function setStartDateAttribute($date)
    // {
    //     $this->attributes['start_date'] = Carbon::parse($date);
    // }

    // public function setEndDateAttribute($date)
    // {
    //     $this->attributes['end_date'] = Carbon::parse($date);
    // }

    // public function getStartDateAttribute($date)
    // {
    //     return Carbon::parse($date)->format('Y-m-d');
    // }

    // public function getEndDateAttribute($date)
    // {
    //     return Carbon::parse($date)->format('Y-m-d');
    // }

    public function category()
{
    return $this->belongsTo(Category::class);
}

public function user()
{
    return $this->belongsTo(Admin::class);
}

}