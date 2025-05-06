<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    
    protected $fillable = [
        "name",
        "description",
        "prix_achat",
        "prix_ht",
        "prix_ttc",
        "categorie_id",
        "provider_id",
        "reference",
        "type",
        "tva",
        "min_qty",
        "stock_quantity",
    ];

    /**
     * Détermine si le produit est variable
     */
    public function isVariable()
    {
        return $this->type == 1;
    }

    /**
     * Détermine si le produit est simple
     */
    public function isSimple()
    {
        return $this->type == 0;
    }

    /**
     * Relation avec la catégorie
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    /**
     * Relation avec le fournisseur
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    /**
     * Relation avec les variations
     */
    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    /**
     * Relation avec les images du produit
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Obtenir tous les attributs du produit
     */
    public function getAttribs()
    {
        $attributes = [];
        
        // Collecter tous les attributs utilisés dans les variations
        foreach ($this->variations as $variation) {
            foreach ($variation->attributeValues as $attributeValue) {
                $attributeId = $attributeValue->attribute_id;
                $attributeName = $attributeValue->attribute->name;
                
                if (!isset($attributes[$attributeId])) {
                    $attributes[$attributeId] = [
                        'name' => $attributeName,
                        'values' => []
                    ];
                }
                
                // Ajouter la valeur si elle n'existe pas déjà
                $valueId = $attributeValue->id;
                if (!in_array($valueId, array_column($attributes[$attributeId]['values'], 'id'))) {
                    $attributes[$attributeId]['values'][] = [
                        'id' => $valueId,
                        'value' => $attributeValue->value
                    ];
                }
            }
        }
        
        return $attributes;
    }
}