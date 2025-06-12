<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la liste des clients avec statistiques
     */
    public function index()
    {
        // Statistiques globales
        $totalClients = Client::count();
        $activeClients = Client::whereHas('orders')->count();
        $newClientsThisMonth = Client::whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year)
                                   ->count();
        
        $avgOrdersPerClient = Client::withCount('orders')
                                  ->get()
                                  ->avg('orders_count');

        // Listes pour les filtres
        $cities = Client::distinct()->pluck('city')->filter()->sort()->values();
        $delegations = Client::distinct()->pluck('delegation')->filter()->sort()->values();

        return view('clients.index', compact(
            'totalClients',
            'activeClients', 
            'newClientsThisMonth',
            'avgOrdersPerClient',
            'cities',
            'delegations'
        ));
    }

    /**
     * API pour récupérer les clients avec DataTables
     */
    public function getClients(Request $request)
    {
        if ($request->ajax()) {
            $clients = Client::withCount('orders')
                           ->with(['orders' => function($query) {
                               $query->latest()->limit(1);
                           }])
                           ->select('clients.*');

            return $this->buildClientsDataTable($clients, $request);
        }
        return abort(404);
    }

    /**
     * API pour récupérer les statistiques filtrées
     */
    public function getStats(Request $request)
    {
        $query = Client::query();

        // Appliquer les filtres
        $this->applyFilters($query, $request);

        $totalClients = $query->count();
        $activeClients = (clone $query)->whereHas('orders')->count();
        
        $newClientsQuery = clone $query;
        if ($request->date_from && $request->date_to) {
            // Si des dates sont spécifiées, utiliser cette période
            $newClientsThisMonth = $newClientsQuery->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ])->count();
        } else {
            // Sinon, utiliser le mois courant
            $newClientsThisMonth = $newClientsQuery->whereMonth('created_at', Carbon::now()->month)
                                                 ->whereYear('created_at', Carbon::now()->year)
                                                 ->count();
        }

        $avgOrdersPerClient = (clone $query)->withCount('orders')
                                          ->get()
                                          ->avg('orders_count');

        return response()->json([
            'totalClients' => $totalClients,
            'activeClients' => $activeClients,
            'newClientsThisMonth' => $newClientsThisMonth,
            'avgOrdersPerClient' => number_format($avgOrdersPerClient, 1)
        ]);
    }

    /**
     * Construit le DataTable pour les clients
     */
    private function buildClientsDataTable($clients, Request $request)
    {
        return DataTables::of($clients)
            ->addColumn('client_info', function ($client) {

                $initials = strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1));
                $initials = mb_convert_encoding($initials , 'UTF-8', 'UTF-8');
                return '
                    <div class="client-info">
                        <div class="client-avatar">' . $initials . '</div>
                        <div class="client-details">
                            <small>Client #' . $client->id . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('phones', function ($client) {
                $phones = '<span class="phone-badge"><i class="fas fa-phone"></i> ' . $client->phone . '</span>';
                if ($client->phone2) {
                    $phones .= '<br><span class="phone-badge"><i class="fas fa-mobile-alt"></i> ' . $client->phone2 . '</span>';
                }
                return $phones;
            })
            ->addColumn('location', function ($client) {
                return '
                    <div class="location-info">
                        <span class="city-badge">' . $client->city . '</span>
                        <span class="delegation-text">' . $client->delegation . '</span>
                    </div>
                ';
            })
            ->addColumn('orders_stats', function ($client) {
                $lastOrder = $client->orders->first();
                $lastOrderText = $lastOrder ? 
                    'Dernière: ' . $lastOrder->created_at->format('d/m/Y') :
                    'Aucune commande';
                
                return '
                    <div class="order-stats">
                        <div class="order-count">' . $client->orders_count . '</div>
                        <div class="last-order">' . $lastOrderText . '</div>
                    </div>
                ';
            })
            ->addColumn('created_at_formatted', function ($client) {
                return $client->created_at->format('d/m/Y H:i');
            })
            ->addColumn('action', function ($client) {
                $buttons = '';
                $buttons .= '<a href="' . route('clients.show', $client->id) . '" class="btn btn-sm btn-info mr-1 mb-1" title="Voir"><i class="fas fa-eye"></i></a>';
                $buttons .= '<a href="' . route('clients.edit', $client->id) . '" class="btn btn-sm btn-primary mr-1 mb-1" title="Modifier"><i class="fas fa-edit"></i></a>';
                
                // Bouton pour créer une commande pour ce client
                $buttons .= '<a href="' . route('orders.create', ['client_id' => $client->id]) . '" class="btn btn-sm btn-success mr-1 mb-1" title="Nouvelle commande"><i class="fas fa-plus"></i></a>';
                
                $buttons .= '<form action="' . route('clients.destroy', $client->id) . '" method="POST" style="display:inline;" class="mr-1">';
                $buttons .= csrf_field();
                $buttons .= method_field('DELETE');
                $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1" title="Supprimer" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce client ?\')"><i class="fas fa-trash"></i></button>';
                $buttons .= '</form>';
                
                return $buttons;
            })
            ->filter(function ($query) use ($request) {
                // Recherche globale
                if ($request->has('search') && !empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('phone2', 'like', "%{$search}%")
                          ->orWhere('city', 'like', "%{$search}%")
                          ->orWhere('delegation', 'like', "%{$search}%")
                          ->orWhere('address', 'like', "%{$search}%");
                    });
                }

                // Appliquer les filtres spécifiques
                $this->applyFilters($query, $request);
            })
            ->rawColumns(['client_info', 'phones', 'location', 'orders_stats', 'action'])
            ->make(true);
    }

    /**
     * Applique les filtres communs
     */
    private function applyFilters($query, Request $request)
    {
        // Filtre par ville
        if ($request->has('city') && !empty($request->city)) {
            $query->where('city', $request->city);
        }

        // Filtre par délégation
        if ($request->has('delegation') && !empty($request->delegation)) {
            $query->where('delegation', $request->delegation);
        }

        // Filtre par nombre de commandes
        if ($request->has('orders_filter') && !empty($request->orders_filter)) {
            switch ($request->orders_filter) {
                case '0':
                    $query->doesntHave('orders');
                    break;
                case '1-5':
                    $query->has('orders', '>=', 1)->has('orders', '<=', 5);
                    break;
                case '6-10':
                    $query->has('orders', '>=', 6)->has('orders', '<=', 10);
                    break;
                case '10+':
                    $query->has('orders', '>', 10);
                    break;
            }
        }

        // Filtre par période d'inscription
        if ($request->has('date_from') && $request->has('date_to') && 
            !empty($request->date_from) && !empty($request->date_to)) {
            $from = date('Y-m-d 00:00:00', strtotime($request->date_from));
            $to = date('Y-m-d 23:59:59', strtotime($request->date_to));
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    /**
     * Affiche les détails d'un client
     */
    public function show(Client $client)
    {
        $client->load(['orders' => function($query) {
            $query->with(['deliveryCompany', 'user'])->latest();
        }]);

        // Statistiques du client
        $totalOrders = $client->orders->count();
        $totalSpent = $client->orders->sum('total');
        $avgOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
        
        $statusCounts = $client->orders->groupBy('status')->map->count();

        return view('clients.show', compact('client', 'totalOrders', 'totalSpent', 'avgOrderValue', 'statusCounts'));
    }

    /**
     * Formulaire de création d'un client
     */
    public function create()
    {
        $delegations = [
            'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan',
            'Bizerte', 'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Sousse',
            'Monastir', 'Mahdia', 'Sfax', 'Kairouan', 'Kasserine', 'Sidi Bouzid',
            'Gabès', 'Medenine', 'Tataouine', 'Gafsa', 'Tozeur', 'Kebili'
        ];

        return view('clients.create', compact('delegations'));
    }

    /**
     * Enregistre un nouveau client
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:clients,phone',
            'phone2' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'delegation' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $client = Client::create($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès');
    }

    /**
     * Formulaire d'édition d'un client
     */
    public function edit(Client $client)
    {
        $delegations = [
            'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan',
            'Bizerte', 'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Sousse',
            'Monastir', 'Mahdia', 'Sfax', 'Kairouan', 'Kasserine', 'Sidi Bouzid',
            'Gabès', 'Medenine', 'Tataouine', 'Gafsa', 'Tozeur', 'Kebili'
        ];

        return view('clients.edit', compact('client', 'delegations'));
    }

    /**
     * Met à jour un client
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'phone' => 'required|string|unique:clients,phone,' . $client->id,
            'phone2' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'delegation' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'Client mis à jour avec succès');
    }

    /**
     * Supprime un client
     */
    public function destroy(Client $client)
    {
        try {
            // Vérifier s'il a des commandes
            if ($client->orders()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce client car il a des commandes associées.');
            }

            $client->delete();

            return redirect()->route('clients.index')
                ->with('success', 'Client supprimé avec succès');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du client: ' . $e->getMessage());
        }
    }

    /**
     * Recherche AJAX pour l'autocomplétion
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $clients = Client::where(function($query) use ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        })
        ->limit(10)
        ->get(['id', 'first_name', 'last_name', 'phone', 'city']);

        return response()->json($clients);
    }
}