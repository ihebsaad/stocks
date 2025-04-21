<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
    ];

    /**
     * Relation avec l'attribut parent
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Relation avec les variations qui utilisent cette valeur
     */
    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'variation_attribute_values');
    }
}