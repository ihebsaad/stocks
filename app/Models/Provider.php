<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $table = "providers";

	protected $fillable = [
        'name',
        'lastname',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal',
        'company',
		'email_contact',
		'phone_contact'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
