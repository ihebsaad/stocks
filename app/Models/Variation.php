<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'reference',
        'prix_achat',
        'prix_ht',
        'prix_ttc',
        'stock_quantity',
    ];

    /**
     * Relation avec le produit parent
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation avec les valeurs d'attributs de cette variation
     */
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'variation_attribute_values');
    }

    /**
     * Obtenir le nom formaté de la variation (ex: "Rouge - XL")
     */
    public function getFormattedName()
    {
        $parts = [];
        
        foreach ($this->attributeValues as $attributeValue) {
            $parts[] = $attributeValue->value;
        }
        
        return implode(' - ', $parts);
    }
}