<?php

namespace App\Services;

use App\Models\Parcel;
use App\Models\DeliveryCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParcelStatisticsService
{
    // Mapping des statuts pour chaque société de livraison
    private $statusMapping = [
        2 => [
            'created' => ['Colis Créé', 'Colis à été modifié'],
            'in_transit' => ['C.R Transferé', 'Br imprimé', 'En transit', 'Console B Sousse'],
            'delivered' => ['Colis livré'],
            'returned' => ['Retour facturé', 'Retourné Dépot']
        ],
        3 => [
            'created' => ['Colis créé', 'Colis modifier'],
            'in_transit' => ['En cours', 'Reporté', 'Collecté - Tunis'],
            'delivered' => ['Livré - espèce', 'Colis livré', 'Payé'],
            'returned' => ['Retour definitif', 'Retour expéditeur'],
            'pending' => ['Non recu'],
            'exchanged' => ['Echange clôturé']
        ]
    ];

    /**
     * Obtenir les statistiques principales des colis
     */
    public function getMainStatistics($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        
        // Appliquer les filtres
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get();
        
        // Calculer les statistiques générales
        $stats = [
            'total_parcels' => $parcels->count(),
            'total_amount' => $parcels->sum('cod'),
            'created_count' => 0,
            'in_transit_count' => 0,
            'delivered_count' => 0,
            'returned_count' => 0,
            'pending_count' => 0,
            'exchanged_count' => 0,
            'created_amount' => 0,
            'in_transit_amount' => 0,
            'delivered_amount' => 0,
            'returned_amount' => 0,
            'pending_amount' => 0,
            'exchanged_amount' => 0,
        ];

        // Analyser chaque colis
        foreach ($parcels as $parcel) {
            $category = $this->getStatusCategory($parcel->dernier_etat, $parcel->delivery_company_id);
            
            if ($category) {
                $stats[$category . '_count']++;
                $stats[$category . '_amount'] += $parcel->cod;
            }
        }

        return $stats;
    }

    /**
     * Obtenir les statistiques par société de livraison
     */
    public function getStatisticsByCompany($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get()->groupBy('delivery_company_id');
        $companies = DeliveryCompany::whereIn('id', $parcels->keys())->get()->keyBy('id');
        
        $stats = [];
        
        foreach ($parcels as $companyId => $companyParcels) {
            $company = $companies->get($companyId);
            if (!$company) continue;
            
            $companyStats = [
                'company_name' => $company->name,
                'total_parcels' => $companyParcels->count(),
                'total_amount' => $companyParcels->sum('cod'),
                'created_count' => 0,
                'in_transit_count' => 0,
                'delivered_count' => 0,
                'returned_count' => 0,
                'pending_count' => 0,
                'exchanged_count' => 0,
                'created_amount' => 0,
                'in_transit_amount' => 0,
                'delivered_amount' => 0,
                'returned_amount' => 0,
                'pending_amount' => 0,
                'exchanged_amount' => 0,
            ];
            
            foreach ($companyParcels as $parcel) {
                $category = $this->getStatusCategory($parcel->dernier_etat, $companyId);
                
                if ($category) {
                    $companyStats[$category . '_count']++;
                    $companyStats[$category . '_amount'] += $parcel->cod;
                }
            }
            
            $stats[$companyId] = $companyStats;
        }
        
        return $stats;
    }

    /**
     * Obtenir les statistiques détaillées par statut
     */
    public function getDetailedStatusStatistics($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get();
        
        $stats = [];
        
        foreach ($parcels as $parcel) {
            $status = $parcel->dernier_etat ?: 'Non défini';
            $companyId = $parcel->delivery_company_id;
            
            if (!isset($stats[$companyId])) {
                $stats[$companyId] = [
                    'company_name' => $parcel->company->name ?? 'Inconnue',
                    'statuses' => []
                ];
            }
            
            if (!isset($stats[$companyId]['statuses'][$status])) {
                $stats[$companyId]['statuses'][$status] = [
                    'count' => 0,
                    'amount' => 0,
                    'category' => $this->getStatusCategory($status, $companyId)
                ];
            }
            
            $stats[$companyId]['statuses'][$status]['count']++;
            $stats[$companyId]['statuses'][$status]['amount'] += $parcel->cod;
        }
        
        return $stats;
    }

    /**
     * Obtenir les statistiques par période
     */
    public function getStatisticsByPeriod($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $period = $filters['period'] ?? 'daily';
        $dateFormat = $this->getDateFormat($period);
        
        $parcels = $query->get();
        
        $stats = [];
        
        foreach ($parcels as $parcel) {
            $date = $parcel->created_at->format($dateFormat);
            
            if (!isset($stats[$date])) {
                $stats[$date] = [
                    'date' => $date,
                    'total_parcels' => 0,
                    'total_amount' => 0,
                    'created_count' => 0,
                    'in_transit_count' => 0,
                    'delivered_count' => 0,
                    'returned_count' => 0,
                    'pending_count' => 0,
                    'exchanged_count' => 0,
                ];
            }
            
            $stats[$date]['total_parcels']++;
            $stats[$date]['total_amount'] += $parcel->cod;
            
            $category = $this->getStatusCategory($parcel->dernier_etat, $parcel->delivery_company_id);
            if ($category) {
                $stats[$date][$category . '_count']++;
            }
        }
        
        return array_values($stats);
    }

    /**
     * Obtenir la catégorie d'un statut selon la société de livraison
     */
    private function getStatusCategory($status, $companyId)
    {
        if (!$status || !isset($this->statusMapping[$companyId])) {
            return null;
        }
        
        foreach ($this->statusMapping[$companyId] as $category => $statuses) {
            if (in_array($status, $statuses)) {
                return $category;
            }
        }
        
        return null;
    }

    /**
     * Appliquer les filtres à la requête
     */
    private function applyFilters($query, $filters)
    {
        // Filtre par période
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay()
            ]);
        }
        
        // Filtre par société de livraison
        if (isset($filters['delivery_company_id'])) {
            $query->where('delivery_company_id', $filters['delivery_company_id']);
        }
        
        // Filtre par statut
        if (isset($filters['status'])) {
            $query->where('dernier_etat', $filters['status']);
        }
        
        return $query;
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
     * Obtenir les métriques de performance
     */
    public function getPerformanceMetrics($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get();
        
        $metrics = [
            'delivery_rate' => 0,
            'return_rate' => 0,
            'average_delivery_time' => 0,
            'total_revenue' => 0,
            'companies_performance' => []
        ];
        
        if ($parcels->count() > 0) {
            $delivered = $parcels->filter(function($parcel) {
                return $this->getStatusCategory($parcel->dernier_etat, $parcel->delivery_company_id) === 'delivered';
            });
            
            $returned = $parcels->filter(function($parcel) {
                return $this->getStatusCategory($parcel->dernier_etat, $parcel->delivery_company_id) === 'returned';
            });
            
            $metrics['delivery_rate'] = ($delivered->count() / $parcels->count()) * 100;
            $metrics['return_rate'] = ($returned->count() / $parcels->count()) * 100;
            $metrics['total_revenue'] = $delivered->sum('cod');
            
            // Calculer le temps moyen de livraison
            $deliveryTimes = $delivered->filter(function($parcel) {
                return $parcel->date_dernier_etat;
            })->map(function($parcel) {
                return Carbon::parse($parcel->date_dernier_etat)->diffInDays($parcel->created_at);
            });
            
            if ($deliveryTimes->count() > 0) {
                $metrics['average_delivery_time'] = $deliveryTimes->avg();
            }
        }
        
        return $metrics;
    }
}