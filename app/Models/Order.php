<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'delivery_company_id',
        'service_type',
        'status',
        'notes',
        'free_delivery',
        'subtotal',
        'discount',
        'delivery_cost',
        'total',
    ];

    protected $casts = [
        'free_delivery' => 'boolean',
    ];

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function deliveryCompany()
    {
        return $this->belongsTo(DeliveryCompany::class);
    }

    public function images()
    {
        return $this->hasMany(OrderImage::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    
    public function parcel()
    {
        return $this->hasOne(Parcel::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    
    // Ajouter une entrée dans l'historique des statuts
    public function addStatusHistory($oldStatus, $newStatus, $comment = null)
    {
        return $this->statusHistory()->create([
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
        ]);
    }
}