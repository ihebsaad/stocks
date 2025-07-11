<?php

namespace App\Http\Controllers;

use App\Services\ParcelStatisticsService;
use App\Models\DeliveryCompany;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(ParcelStatisticsService $statisticsService)
    {
        $this->middleware('auth');
        $this->statisticsService = $statisticsService;
    }

    /**
     * Afficher le tableau de bord des statistiques
     */
    public function index(Request $request) 
    {
        $filters = $this->getFilters($request);
        
        // Ajouter la période pour le graphique si elle n'est pas spécifiée
        if (!isset($filters['period'])) {
            $filters['period'] = 'weekly'; // Par défaut hebdomadaire
        }
        
        // Statistiques principales (globales)
        $mainStats = $this->statisticsService->getMainStatistics($filters);
        
        // Statistiques par société
        $companyStats = $this->statisticsService->getStatisticsByCompany($filters);
        
        // Métriques de performance
        $performanceMetrics = $this->statisticsService->getPerformanceMetrics($filters);
        
        // Données pour les graphiques (avec la période spécifiée)
        $periodStats = $this->statisticsService->getStatisticsByPeriod($filters);
        
        // Statistiques pour le graphique en secteurs
        $statusStats = $this->statisticsService->getStatusStatsForChart($filters);
        
        // Sociétés de livraison pour le filtre
        $deliveryCompanies = DeliveryCompany::where('is_active', true)->get();
        
        return view('parcels.stats', compact(
            'mainStats',
            'companyStats', 
            'performanceMetrics',
            'periodStats',
            'statusStats',
            'deliveryCompanies',
            'filters'
        ));
    }

 
 
    /**
     * Obtenir les filtres depuis la requête
     */
    private function getFilters(Request $request)
    {
        $filters = [];
        
        // Filtre par période
        if ($request->has('date_from') && $request->has('date_to')) {
            $filters['date_from'] = $request->date_from;
            $filters['date_to'] = $request->date_to;
        } else {
            // Par défaut, derniers 30 jours
            $filters['date_from'] = Carbon::now()->subDays(30)->format('Y-m-d');
            $filters['date_to'] = Carbon::now()->format('Y-m-d');
        }
        
        // Filtre par société de livraison
        if ($request->has('delivery_company_id') && $request->delivery_company_id != '') {
            $filters['delivery_company_id'] = $request->delivery_company_id;
        }
        
        // Filtre par statut
        if ($request->has('status') && $request->status != '') {
            $filters['status'] = $request->status;
        }
        
        // Période pour les graphiques
        if ($request->has('period')) {
            $filters['period'] = $request->get('period', 'weekly');
        }
        
        return $filters;
    }
}