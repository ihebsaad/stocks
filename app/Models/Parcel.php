<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $fillable = [
        'order_id', 'delivery_company_id', 'tel_l', 'tel2_l', 'nom_client',
        'gov_l', 'adresse_l', 'cod', 'libelle', 'nb_piece', 'remarque', 'service', 'status'
    ];

    public function company() {
		return $this->belongsTo(DeliveryCompany::class, 'delivery_company_id'); 
	}
	
    public function order() {
		return $this->belongsTo(Order::class); 
	}
}
