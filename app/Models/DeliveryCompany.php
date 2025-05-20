<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'delivery_price',
        'manager_name',
        'phone',
        'api_url_dev',
        'api_url_prod',
        'code_api',
        'cle_api',
        'is_active',
    ];

    protected $casts = [
        'delivery_price' => 'decimal:3',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Formatage du prix pour l'affichage
    public function getFormattedPriceAttribute()
    {
        return number_format($this->delivery_price, 3, ',', ' ') . ' DT';
    }

}