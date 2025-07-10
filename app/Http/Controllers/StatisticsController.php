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
        
        // Statistiques principales (globales)
        $mainStats = $this->statisticsService->getMainStatistics($filters);
        
        // Statistiques par société
        $companyStats = $this->statisticsService->getStatisticsByCompany($filters);
        
        // Métriques de performance
        $performanceMetrics = $this->statisticsService->getPerformanceMetrics($filters);
        
        // Données pour les graphiques
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
     * API pour les données de graphiques
     */
    public function getChartData(Request $request)
    {
        $filters = $this->getFilters($request);
        $chartType = $request->get('chart_type', 'period');
        
        switch ($chartType) {
            case 'period':
                return response()->json($this->statisticsService->getStatisticsByPeriod($filters));
            
            case 'company':
                return response()->json($this->statisticsService->getStatisticsByCompany($filters));
            
            case 'status':
                return response()->json($this->statisticsService->getStatusStatsForChart($filters));
            
            default:
                return response()->json([]);
        }
    }

    /**
     * Exporter les statistiques en PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'mainStats' => $this->statisticsService->getMainStatistics($filters),
            'companyStats' => $this->statisticsService->getStatisticsByCompany($filters),
            'performanceMetrics' => $this->statisticsService->getPerformanceMetrics($filters),
            'filters' => $filters,
            'generated_at' => now()->format('d/m/Y H:i')
        ];
        
        $pdf = \PDF::loadView('statistics.pdf', $data);
        return $pdf->download('statistiques_colis_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exporter les statistiques en Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = $this->getFilters($request);
        
        // Vous devrez implémenter cette méthode dans votre service
        return $this->statisticsService->exportToExcel($filters);
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
            $filters['period'] = $request->period;
        }
        
        return $filters;
    }
}