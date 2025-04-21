<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntryItem extends Model
{
    protected $fillable = [
        'stock_entry_id',
        'product_id',
        'variation_id',
        'quantity',
        'prix_achat',
    ];
    
    /**
     * L'entrée de stock parente
     */
    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }
    
    /**
     * Le produit associé
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * La variation associée (si applicable)
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
