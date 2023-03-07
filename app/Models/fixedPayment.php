<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class fixedPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'type',
        'amount',
        'currency',
        'admin_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'category_id',
        'category_title',
    ];

    protected $appends = ['categoryTitle']; // add the attribute name here

    public function getCategoryTitleAttribute()
    {
        return $this->category->title; // use the correct attribute name here
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

     
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categoryTitle(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'title');
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
}
