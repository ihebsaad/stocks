<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupSlip extends Model
{
    protected $fillable = [
        'date', 'reference', 'delivery_company_id', 'user_id', 'status', 'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function deliveryCompany()
    {
        return $this->belongsTo(DeliveryCompany::class);
    }

    public function parcels()
    {
        return $this->belongsToMany(Parcel::class, 'pickup_slip_parcels');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
