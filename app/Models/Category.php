<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Income;
use App\Http\Admin;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
        'deleted_by',
        'admin_id',
    ];

    public function income(): HasMany
    {
        
        return $this->hasMany(Income::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
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
