<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\fixedPayment;
use App\Models\recurringPayment;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'is_super',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function fixedPayments(): HasMany
    {
        return $this->hasMany(fixedPayment::class);
    }

    public function recurringPayments(): HasMany
    {
        return $this->hasMany(recurringPayment::class);
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

    public function categories(){
        return $this->hasMany(Category::class);
    }



}
