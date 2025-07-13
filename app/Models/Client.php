<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'phone',
        'phone2',
        'first_name',
        'last_name',
        'city',
        'delegation',
        'address',
        'postal_code',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    // Récupère le nom complet du client
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function promoCodes()
    {
        return $this->hasMany(PromoCode::class);
    }

}