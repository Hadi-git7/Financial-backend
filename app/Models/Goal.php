<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin;
use Carbon\Carbon;

class Goal extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'profit',
        'year',
        'created_by',
        'updated_by',
        'deleted_by',
        'admin_id',
    ];

    public function user(){
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