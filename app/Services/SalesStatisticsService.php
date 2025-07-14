<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockEntryItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesStatisticsService
{
    /**
     * Obtenir les statistiques de bénéfices pour la période donnée
     */
    public function getProfitStatistics($filters)
    {
        $query = OrderItem::with(['product', 'variation', 'order'])
            ->whereHas('order', function ($q) use ($filters) {
                $q->where('status', '!=', 'cancelled'); // Exclure les commandes annulées
                
                if (isset($filters['date_from']) && $filters['date_from']) {
                    $q->whereDate('created_at', '>=', $filters['date_from']);
                }
                if (isset($filters['date_to']) && $filters['date_to']) {
                    $q->whereDate('created_at', '<=', $filters['date_to']);
                }
            });

        $orderItems = $query->get();
        
        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;
        $profitMargin = 0;
        
        foreach ($orderItems as $item) {
            $revenue = $item->subtotal;
            $cost = $this->calculateItemCost($item);
            
            $totalRevenue += $revenue;
            $totalCost += $cost;
            $totalProfit += ($revenue - $cost);
        }
        
        if ($totalRevenue > 0) {
            $profitMargin = ($totalProfit / $totalRevenue) * 100;
        }
        
        return [
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'profit_margin' => round($profitMargin, 2),
            'items_count' => $orderItems->count()
        ];
    }
    
    /**
     * Obtenir le top 10 des produits les plus vendus
     */
    public function getTopSellingProducts($filters, $limit = 10)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('variations', 'order_items.variation_id', '=', 'variations.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.reference as product_reference',
                'variations.reference as variation_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count')
            )
            ->where('orders.status', '!=', 'cancelled');
            
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('orders.created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('orders.created_at', '<=', $filters['date_to']);
        }
        
        $topProducts = $query
            ->groupBy('products.id', 'products.name', 'products.reference', 'variations.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
            
        // Calculer les bénéfices pour chaque produit
        $topProductsWithProfit = $topProducts->map(function ($item) use ($filters) {
            $profit = $this->calculateProductProfit($item->product_id, $filters);
            
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_reference' => $item->product_reference,
                'variation_name' => $item->variation_name,
                'total_quantity' => $item->total_quantity,
                'total_revenue' => $item->total_revenue,
                'orders_count' => $item->orders_count,
                'profit' => $profit,
                'profit_margin' => $item->total_revenue > 0 ? round(($profit / $item->total_revenue) * 100, 2) : 0
            ];
        });
        
        return $topProductsWithProfit;
    }
    
    /**
     * Obtenir les statistiques de bénéfices par période
     */
    public function getProfitByPeriod($filters)
    {
        $period = $filters['period'] ?? 'weekly';
        $dateFormat = $this->getDateFormat($period);
        $groupFormat = $this->getGroupFormat($period);
        
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw("$groupFormat as period"),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('SUM(order_items.quantity) as quantity')
            )
            ->where('orders.status', '!=', 'cancelled');
            
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('orders.created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('orders.created_at', '<=', $filters['date_to']);
        }
        
        $results = $query
            ->groupBy('period')
            ->orderBy('period')
            ->get();
            
        // Calculer les coûts et bénéfices pour chaque période
        $profitByPeriod = $results->map(function ($item) use ($filters, $period) {
            // Estimation du coût basée sur la proportion du chiffre d'affaires
            $totalStats = $this->getProfitStatistics($filters);
            $costRatio = $totalStats['total_revenue'] > 0 ? $totalStats['total_cost'] / $totalStats['total_revenue'] : 0;
            
            $estimatedCost = $item->revenue * $costRatio;
            $profit = $item->revenue - $estimatedCost;
            
            return [
                'period' => $item->period,
                'revenue' => $item->revenue,
                'cost' => $estimatedCost,
                'profit' => $profit,
                'quantity' => $item->quantity,
                'profit_margin' => $item->revenue > 0 ? round(($profit / $item->revenue) * 100, 2) : 0
            ];
        });
        
        return $profitByPeriod;
    }
    
    /**
     * Calculer le coût d'un article de commande
     */
    private function calculateItemCost($orderItem)
    {
        $product = $orderItem->product;
        $quantity = $orderItem->quantity;
        
        // Si c'est un produit variable, utiliser le prix d'achat de la variation
        if ($orderItem->variation_id && $orderItem->variation) {
            $purchasePrice = $orderItem->variation->prix_achat ?? $product->prix_achat;
        } else {
            $purchasePrice = $product->prix_achat;
        }
        
        // Si pas de prix d'achat défini, essayer de le calculer à partir des entrées de stock
        if (!$purchasePrice) {
            $purchasePrice = $this->getAveragePurchasePrice($orderItem->product_id, $orderItem->variation_id);
        }
        
        return $purchasePrice * $quantity;
    }
    
    /**
     * Calculer le bénéfice d'un produit pour la période donnée
     */
    private function calculateProductProfit($productId, $filters)
    {
        $query = OrderItem::with(['product', 'variation'])
            ->where('product_id', $productId)
            ->whereHas('order', function ($q) use ($filters) {
                $q->where('status', '!=', 'cancelled');
                
                if (isset($filters['date_from']) && $filters['date_from']) {
                    $q->whereDate('created_at', '>=', $filters['date_from']);
                }
                if (isset($filters['date_to']) && $filters['date_to']) {
                    $q->whereDate('created_at', '<=', $filters['date_to']);
                }
            });
            
        $orderItems = $query->get();
        
        $totalRevenue = 0;
        $totalCost = 0;
        
        foreach ($orderItems as $item) {
            $totalRevenue += $item->subtotal;
            $totalCost += $this->calculateItemCost($item);
        }
        
        return $totalRevenue - $totalCost;
    }
    
    /**
     * Obtenir le prix d'achat moyen d'un produit à partir des entrées de stock
     */
    private function getAveragePurchasePrice($productId, $variationId = null)
    {
        $query = StockEntryItem::where('product_id', $productId);
        
        if ($variationId) {
            $query->where('variation_id', $variationId);
        }
        
        $avgPrice = $query->avg('prix_achat');
        
        return $avgPrice ?? 0;
    }
    
    /**
     * Obtenir le format de date selon la période
     */
    private function getDateFormat($period)
    {
        switch ($period) {
            case 'daily':
                return 'Y-m-d';
            case 'weekly':
                return 'Y-W';
            case 'monthly':
                return 'Y-m';
            case 'yearly':
                return 'Y';
            default:
                return 'Y-m-d';
        }
    }
    
    /**
     * Obtenir le format de groupement SQL selon la période
     */
    private function getGroupFormat($period)
    {
        switch ($period) {
            case 'daily':
                return "DATE(orders.created_at)";
            case 'weekly':
                return "YEARWEEK(orders.created_at, 1)";
            case 'monthly':
                return "DATE_FORMAT(orders.created_at, '%Y-%m')";
            case 'yearly':
                return "YEAR(orders.created_at)";
            default:
                return "DATE(orders.created_at)";
        }
    }
}