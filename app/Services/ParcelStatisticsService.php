<?php

namespace App\Services;

use App\Models\Parcel;
use App\Models\DeliveryCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParcelStatisticsService
{
    // Mapping des statuts pour chaque société de livraison - Sans regroupement excessif
    private $statusMapping = [
        2 => [
            'created' => ['Colis Créé'], // Seuls ces deux sont regroupés
            'updated' => ['Colis à été modifié'], // Seuls ces deux sont regroupés
            'br_printed' => ['Br imprimé'],
            'transferred' => ['C.R Transferé'],
            'in_transit' => ['En transit'],
            'returned_charged' => ['Retour facturé'],
            'inbound' => ['Console Inbound'],
            'dated' => ['Colis daté'],
            'wha' => ['Wha'],
            'other'=> ['Autre'],
        ],
        3 => [
            'created' => ['Colis créé'], // Seuls ces deux sont regroupés
            'updated' => ['Colis modifier'], // Seuls ces deux sont regroupés
            'in_progress' => ['En cours'],
            'postponed' => ['Reporté'],
            'collected_tunis' => ['Collecté - Tunis'],
            'delivered' => ['Colis livré'],
            'paid' => ['Payé'],
            'definitive_return' => ['Retour definitif'],
            'return_sender' => ['Retour expéditeur'],
            'not_received' => ['Non recu'],
            'exchange_closed' => ['Echange clôturé'],
            'other'=> ['Autre'],

        ]
    ];

    // Configuration des couleurs pour chaque statut
    private $statusColors = [
        'created' => ['color' => '#3B82F6', 'bg' => 'rgba(59, 130, 246, 0.1)', 'icon' => 'fas fa-plus-circle'],
        'updated' => ['color' => '#3B82F6', 'bg' => 'rgba(59, 130, 246, 0.1)', 'icon' => 'fas fa-edit'],
        'br_printed' => ['color' => '#8B5CF6', 'bg' => 'rgba(139, 92, 246, 0.1)', 'icon' => 'fas fa-print'],
        'transferred' => ['color' => '#06B6D4', 'bg' => 'rgba(6, 182, 212, 0.1)', 'icon' => 'fas fa-exchange-alt'],
        'in_transit' => ['color' => '#F59E0B', 'bg' => 'rgba(245, 158, 11, 0.1)', 'icon' => 'fas fa-truck'],
        'console_sousse' => ['color' => '#F97316', 'bg' => 'rgba(249, 115, 22, 0.1)', 'icon' => 'fas fa-warehouse'],
        'delivered' => ['color' => '#10B981', 'bg' => 'rgba(16, 185, 129, 0.1)', 'icon' => 'fas fa-check-circle'],
        'delivered_cash' => ['color' => '#059669', 'bg' => 'rgba(5, 150, 105, 0.1)', 'icon' => 'fas fa-money-bill'],
        'paid' => ['color' => '#047857', 'bg' => 'rgba(4, 120, 87, 0.1)', 'icon' => 'fas fa-credit-card'],
        'returned_charged' => ['color' => '#EF4444', 'bg' => 'rgba(239, 68, 68, 0.1)', 'icon' => 'fas fa-undo-alt'],
        'inbound' => ['color' => '#F59E0B', 'bg' => 'rgba(220, 38, 38, 0.1)', 'icon' => 'fas fa-arrow-right'],
        'dated' => ['color' => '#F59E0B', 'bg' => 'rgba(230, 100, 160, 0.1)', 'icon' => 'fas fa-clock-rotate-left'],
        'in_progress' => ['color' => '#8B5CF6', 'bg' => 'rgba(139, 92, 246, 0.1)', 'icon' => 'fas fa-cog'],
        'postponed' => ['color' => '#F59E0B', 'bg' => 'rgba(245, 158, 11, 0.1)', 'icon' => 'fas fa-clock'],
        'collected_tunis' => ['color' => '#06B6D4', 'bg' => 'rgba(6, 182, 212, 0.1)', 'icon' => 'fas fa-map-marker-alt'],
        'definitive_return' => ['color' => '#EF4444', 'bg' => 'rgba(239, 68, 68, 0.1)', 'icon' => 'fas fa-times-circle'],
        'return_sender' => ['color' => '#DC2626', 'bg' => 'rgba(220, 38, 38, 0.1)', 'icon' => 'fas fa-reply'],
        'not_received' => ['color' => '#6B7280', 'bg' => 'rgba(107, 114, 128, 0.1)', 'icon' => 'fas fa-question-circle'],
        'exchange_closed' => ['color' => '#7C3AED', 'bg' => 'rgba(124, 58, 237, 0.1)', 'icon' => 'fas fa-handshake'],
        'other' => ['color' => '#F59E0B', 'bg' => 'rgba(124, rgba(127, 206, 36, 0.1)','icon' =>'fas fa-circle-exclamation'],
        'wha' => ['color' => '#F59E0B', 'bg' => 'rgba(124, rgba(127, 206, 36, 0.1)','icon' =>'fas fa-exclamation']
    ];

    /**
     * Obtenir les statistiques par société avec statuts séparés
     */
    public function getStatisticsByCompany($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get();
        
        // Si une société spécifique est sélectionnée
        if (isset($filters['delivery_company_id'])) {
            return $this->getStatisticsForSpecificCompany($parcels, $filters['delivery_company_id']);
        }
        
        // Sinon, retourner les stats globales
        return $this->getGlobalStatistics($parcels);
    }

    /**
     * Obtenir les statistiques pour une société spécifique
     */
    private function getStatisticsForSpecificCompany($parcels, $companyId)
    {
        $company = DeliveryCompany::find($companyId);
        if (!$company) {
            return [];
        }

        $companyParcels = $parcels->where('delivery_company_id', $companyId);
        $statusStats = [];

        // Grouper par statut exact
        $statusGroups = $companyParcels->groupBy('dernier_etat');

        foreach ($statusGroups as $status => $parcels) {
            $statusKey = $this->getStatusKey($status, $companyId);
            $statusLabel = $this->getStatusLabel($status, $statusKey);
            
            $statusStats[] = [
                'key' => $statusKey,
                'label' => $statusLabel,
                'original_status' => $status,
                'count' => $parcels->count(),
                'amount' => $parcels->sum('cod'),
                'percentage' => $companyParcels->count() > 0 ? 
                    round(($parcels->count() / $companyParcels->count()) * 100, 1) : 0,
                'color' => $this->statusColors[$statusKey]['color'] ?? '#6B7280',
                'bg_color' => $this->statusColors[$statusKey]['bg'] ?? 'rgba(107, 114, 128, 0.1)',
                'icon' => $this->statusColors[$statusKey]['icon'] ?? 'fas fa-circle'
            ];
        }

        return [
            'company_name' => $company->name,
            'total_parcels' => $companyParcels->count(),
            'total_amount' => $companyParcels->sum('cod'),
            'status_stats' => $statusStats
        ];
    }

    /**
     * Obtenir les statistiques globales (toutes sociétés)
     */
    private function getGlobalStatistics($parcels)
    {
        $stats = [];
        $parcelsGrouped = $parcels->groupBy('delivery_company_id');
        
        foreach ($parcelsGrouped as $companyId => $companyParcels) {
            $company = DeliveryCompany::find($companyId);
            if (!$company) continue;
            
            $statusGroups = $companyParcels->groupBy('dernier_etat');
            $statusStats = [];
            
            foreach ($statusGroups as $status => $statusParcels) {
                $statusKey = $this->getStatusKey($status, $companyId);
                $statusLabel = $this->getStatusLabel($status, $statusKey);
                
                $statusStats[] = [
                    'key' => $statusKey,
                    'label' => $statusLabel,
                    'original_status' => $status,
                    'count' => $statusParcels->count(),
                    'amount' => $statusParcels->sum('cod'),
                    'percentage' => $companyParcels->count() > 0 ? 
                        round(($statusParcels->count() / $companyParcels->count()) * 100, 1) : 0,
                    'color' => $this->statusColors[$statusKey]['color'] ?? '#6B7280',
                    'bg_color' => $this->statusColors[$statusKey]['bg'] ?? 'rgba(107, 114, 128, 0.1)',
                    'icon' => $this->statusColors[$statusKey]['icon'] ?? 'fas fa-circle'
                ];
            }
            
            $stats[] = [
                'company_id' => $companyId,
                'company_name' => $company->name,
                'total_parcels' => $companyParcels->count(),
                'total_amount' => $companyParcels->sum('cod'),
                'status_stats' => $statusStats
            ];
        }
        
        return $stats;
    }

    /**
     * Obtenir la clé du statut selon le mapping
     */
    private function getStatusKey($status, $companyId)
    {
        if (!isset($this->statusMapping[$companyId])) {
            return 'other';
        }
        
        foreach ($this->statusMapping[$companyId] as $key => $statuses) {
            if (in_array($status, $statuses)) {
                return $key;
            }
        }
        
        return 'other';
    }

    /**
     * Obtenir le libellé du statut
     */
    private function getStatusLabel($originalStatus, $statusKey)
    {
        // Pour les statuts regroupés (créé/modifié), utiliser le libellé de la clé
        if ($statusKey === 'created') {
            return 'Colis Créé';
        }

        if ($statusKey === 'unknown' || $statusKey === 'other') {
            return 'Autre';
        }
        // Pour les autres, utiliser le statut original
        return $originalStatus;
    }

    /**
     * Obtenir les statistiques par période avec correction des lignes
     */
    public function getStatisticsByPeriod($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $period = $filters['period'] ?? 'daily';
        $parcels = $query->get();
        
        // Créer une plage de dates complète
        $dateRange = $this->getDateRange($filters, $period);
        $stats = [];
        
        // Initialiser toutes les dates avec des valeurs à zéro
        foreach ($dateRange as $date) {
            $stats[$date] = [
                'date' => $date,
                'formatted_date' => $this->formatDateForDisplay($date, $period),
                'total_parcels' => 0,
                'total_amount' => 0,
                'created_count' => 0,
                'in_transit_count' => 0,
                'delivered_count' => 0,
                'returned_count' => 0,
                'pending_count' => 0,
                'other_count' => 0
            ];
        }
        
        // Remplir avec les données réelles
        foreach ($parcels as $parcel) {
            $date = $this->formatDateByPeriod($parcel->created_at, $period);
            
            if (isset($stats[$date])) {
                $stats[$date]['total_parcels']++;
                $stats[$date]['total_amount'] += $parcel->cod;
                
                $category = $this->getGeneralCategory($parcel->dernier_etat, $parcel->delivery_company_id);
                $stats[$date][$category . '_count']++;
            }
        }
        
        return array_values($stats);
    }

    /**
     * Créer une plage de dates complète
     */
    private function getDateRange($filters, $period)
    {
        $startDate = Carbon::parse($filters['date_from'] ?? now()->subDays(30));
        $endDate = Carbon::parse($filters['date_to'] ?? now());
        
        $dates = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dates[] = $this->formatDateByPeriod($current, $period);
            
            switch ($period) {
                case 'daily':
                    $current->addDay();
                    break;
                case 'weekly':
                    $current->addWeek();
                    break;
                case 'monthly':
                    $current->addMonth();
                    break;
            }
        }
        
        return array_unique($dates);
    }

    /**
     * Formater la date selon la période
     */
    private function formatDateByPeriod($date, $period)
    {
        switch ($period) {
            case 'daily':
                return $date->format('Y-m-d');
            case 'weekly':
                return $date->format('Y-W');
            case 'monthly':
                return $date->format('Y-m');
            default:
                return $date->format('Y-m-d');
        }
    }

    /**
     * Formater la date pour l'affichage
     */
    private function formatDateForDisplay($date, $period)
    {
        switch ($period) {
            case 'daily':
                return Carbon::parse($date)->format('d/m');
            case 'weekly':
                return 'S ' ;
            case 'monthly':
                return Carbon::parse($date)->format('m/Y');
            default:
                return $date;
        }
    }

    /**
     * Obtenir la catégorie générale pour les graphiques
     */
    private function getGeneralCategory($status, $companyId)
    {
        $statusKey = $this->getStatusKey($status, $companyId);
        
        // Regroupement pour les graphiques
        if (in_array($statusKey, ['created'])) {
            return 'created';
        } elseif (in_array($statusKey, [ 'in_transit', 'console_sousse', 'in_progress', 'collected_tunis'])) {
            return 'in_transit';
        } elseif (in_array($statusKey, ['delivered', 'delivered_cash', 'paid','br_printed','transferred'])) {
            return 'delivered';
        } elseif (in_array($statusKey, ['returned_charged', 'returned_depot', 'definitive_return', 'return_sender'])) {
            return 'returned';
        } elseif (in_array($statusKey, ['not_received', 'postponed','other','wha','inbound','dated'])) {
            return 'pending';
        }
        
        return 'other';
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
        if (isset($filters['delivery_company_id']) && $filters['delivery_company_id'] !== '') {
            $query->where('delivery_company_id', $filters['delivery_company_id']);
        }
        
        // Filtre par statut
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('dernier_etat', $filters['status']);
        }
        
        return $query;
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
            'total_revenue' => 0
        ];
        
        if ($parcels->count() > 0) {
            $delivered = $parcels->filter(function($parcel) {
                $category = $this->getGeneralCategory($parcel->dernier_etat, $parcel->delivery_company_id);
                return $category === 'delivered';
            });
            
            $returned = $parcels->filter(function($parcel) {
                $category = $this->getGeneralCategory($parcel->dernier_etat, $parcel->delivery_company_id);
                return $category === 'returned';
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









     /**
     * Obtenir les statistiques principales globales
     */
    public function getMainStatistics($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get();
        
        $stats = [
            'total_parcels' => $parcels->count(),
            'total_amount' => $parcels->sum('cod'),
            'created_count' => 0,
            'created_amount' => 0,
            'in_transit_count' => 0,
            'in_transit_amount' => 0,
            'delivered_count' => 0,
            'delivered_amount' => 0,
            'returned_count' => 0,
            'returned_amount' => 0,
            'pending_count' => 0,
            'pending_amount' => 0,
            'other_count' => 0,
            'other_amount' => 0
        ];
        
        foreach ($parcels as $parcel) {
            $category = $this->getGeneralCategory($parcel->dernier_etat, $parcel->delivery_company_id);
            $stats[$category . '_count']++;
            $stats[$category . '_amount'] += $parcel->cod;
        }
        
        return $stats;
    }

    /**
     * Obtenir les statistiques pour le graphique en secteurs
     */
    public function getStatusStatsForChart($filters = [])
    {
        $query = Parcel::with(['order', 'company']);
        $query = $this->applyFilters($query, $filters);
        
        $parcels = $query->get();
        
        $stats = [
            'created' => ['count' => 0, 'amount' => 0],
            'in_transit' => ['count' => 0, 'amount' => 0],
            'delivered' => ['count' => 0, 'amount' => 0],
            'returned' => ['count' => 0, 'amount' => 0],
            'pending' => ['count' => 0, 'amount' => 0],
            'other' => ['count' => 0, 'amount' => 0]
        ];
        
        foreach ($parcels as $parcel) {
            $category = $this->getGeneralCategory($parcel->dernier_etat, $parcel->delivery_company_id);
            $stats[$category]['count']++;
            $stats[$category]['amount'] += $parcel->cod;
        }
        
        // Filtrer les catégories avec des données
        return collect($stats)->filter(function($stat) {
            return $stat['count'] > 0;
        })->map(function($stat, $key) {
            return [
                'label' => $this->getCategoryLabel($key),
                'count' => $stat['count'],
                'amount' => $stat['amount']
            ];
        })->values()->toArray();
    }

    /**
     * Obtenir le libellé d'une catégorie
     */
    private function getCategoryLabel($category)
    {
        $labels = [
            'created' => 'Créés',
            'in_transit' => 'En transit',
            'delivered' => 'Livrés',
            'returned' => 'Retournés',
            'pending' => 'En attente',
            'other' => 'Autres'
        ];
        
        return $labels[$category] ?? 'Inconnu';
    }


}