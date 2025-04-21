<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    protected $fillable = [
        'date',
        'reference',
        'description',
    ];
    
    protected $dates = ['date'];
    
    /**
     * Les éléments de cette entrée de stock
     */
    public function items()
    {
        return $this->hasMany(StockEntryItem::class);
    }
    
    /**
     * Calculer le total de cette entrée de stock
     */
    public function getTotal()
    {
        return $this->items->sum(function($item) {
            return $item->quantity * $item->prix_achat;
        });
    }
}
