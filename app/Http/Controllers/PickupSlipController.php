<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\DeliveryCompany;
use App\Models\PickupSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PickupSlipController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pickupSlips = PickupSlip::with(['deliveryCompany', 'parcels'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

        return view('pickup-slips.index', compact('pickupSlips'));
    }

    public function create()
    {
        $deliveryCompanies = DeliveryCompany::all();
        
        return view('pickup-slips.create', compact('deliveryCompanies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|unique:pickup_slips,reference',
            'delivery_company_id' => 'required|exists:delivery_companies,id',
            'parcels' => 'required|array|min:1',
            'parcels.*' => 'required|exists:parcels,id'
        ]);

        DB::beginTransaction();

        try {
            // Créer le bon de ramassage
            $pickupSlip = PickupSlip::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'delivery_company_id' => $request->delivery_company_id,
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]);

            // Attacher les colis au bon de ramassage
            $pickupSlip->parcels()->attach($request->parcels);

            // Optionnel: Mettre à jour le statut des colis
            Parcel::whereIn('id', $request->parcels)
                  ->update(['status' => 'pickup_scheduled']);

            DB::commit();

            return redirect()->route('pickup.index')
                           ->with('success', 'Bon de ramassage créé avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->with('error', 'Erreur lors de la création du bon de ramassage')
                           ->withInput();
        }
    }

    public function show(PickupSlip $pickupSlip)
    {
        $pickupSlip->load(['deliveryCompany', 'parcels', 'user']);
        dd($pickupSlip);
        $deliveryCompany = DeliveryCompany::find($pickupSlip->delivery_company_id);
        dd($deliveryCompany);
        return view('pickup-slips.show', compact('pickupSlip','deliveryCompany'));
    }

    public function edit(PickupSlip $pickupSlip)
    {
        $deliveryCompanies = DeliveryCompany::all();
        $pickupSlip->load(['parcels']);
        
        return view('pickup-slips.edit', compact('pickupSlip', 'deliveryCompanies'));
    }

    public function update(Request $request, PickupSlip $pickupSlip)
    {
        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|unique:pickup_slips,reference,' . $pickupSlip->id,
            'delivery_company_id' => 'required|exists:delivery_companies,id',
            'parcels' => 'required|array|min:1',
            'parcels.*' => 'required|exists:parcels,id'
        ]);

        DB::beginTransaction();

        try {
            $pickupSlip->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'delivery_company_id' => $request->delivery_company_id
            ]);

            // Synchroniser les colis
            $pickupSlip->parcels()->sync($request->parcels);

            DB::commit();

            return redirect()->route('pickup.index')
                           ->with('success', 'Bon de ramassage mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->with('error', 'Erreur lors de la mise à jour')
                           ->withInput();
        }
    }

    public function destroy(PickupSlip $pickupSlip)
    {
        DB::beginTransaction();

        try {
            // Détacher les colis
            $pickupSlip->parcels()->detach();
            
            // Supprimer le bon de ramassage
            $pickupSlip->delete();

            DB::commit();

            return redirect()->route('pickup.index')
                           ->with('success', 'Bon de ramassage supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression');
        }
    }

    public function data()
    {
        $pickupSlips = PickupSlip::with(['deliveryCompany', 'user'])
                                ->withCount('parcels')
                                ->select('pickup_slips.*');

        return DataTables::of($pickupSlips)
            ->addColumn('delivery_company', function ($pickupSlip) {
                return $pickupSlip->deliveryCompany->name ?? 'N/A';
            })
            ->addColumn('user', function ($pickupSlip) {
                return $pickupSlip->user->name ?? 'N/A';
            })
            ->addColumn('status', function ($pickupSlip) {
                $badgeClass = $this->getStatusBadgeClass($pickupSlip->status);
                return '<span class="badge badge-' . $badgeClass . ' status-badge">' . 
                       ucfirst($pickupSlip->status) . '</span>';
            })
            ->addColumn('actions', function ($pickupSlip) {
                $actions = '<div class="table-actions">';
                
                // Bouton Voir
                $actions .= '<a href="' . route('pickup.show', $pickupSlip->id) . '" 
                           class="btn btn-info btn-sm mr-1" title="Voir">
                           <i class="fas fa-eye"></i>
                           </a>';
                
                // Bouton Modifier
                $actions .= '<a href="' . route('pickup.edit', $pickupSlip->id) . '" 
                           class="btn btn-primary btn-sm mr-1" title="Modifier">
                           <i class="fas fa-edit"></i>
                           </a>';
                
                // Bouton Supprimer
                $actions .= '<button type="button" 
                           class="btn btn-danger btn-sm delete-btn" 
                           data-id="' . $pickupSlip->id . '" 
                           data-reference="' . $pickupSlip->reference . '" 
                           title="Supprimer">
                           <i class="fas fa-trash"></i>
                           </button>';
                
                $actions .= '</div>';
                
                return $actions;
            })
            ->editColumn('date', function ($pickupSlip) {
                return date('d/m/Y', strtotime($pickupSlip->date));
            })
            ->editColumn('created_at', function ($pickupSlip) {
                return $pickupSlip->created_at->format('d/m/Y H:i');
            })
            ->editColumn('parcels_count', function ($pickupSlip) {
                return '<span class="badge badge-secondary">' . $pickupSlip->parcels_count . '</span>';
            })
            ->rawColumns(['status', 'actions', 'parcels_count'])
            ->make(true);
    }

    /**
     * Méthode pour obtenir la classe CSS du badge selon le statut
     */
    private function getStatusBadgeClass($status)
    {
        switch($status) {
            case 'pending': return 'warning';
            case 'in_progress': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'danger';
            default: return 'secondary';
        }
    }


    public function searchParcel(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'delivery_company_id' => 'required|exists:delivery_companies,id'
        ]);

        try {
            $parcel = Parcel::where('reference', $request->barcode)
                           ->where('delivery_company_id', $request->delivery_company_id)
                           ->whereNotIn('status', ['delivered', 'returned'])
                           ->first();

            if (!$parcel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Colis non trouvé ou déjà livré/retourné'
                ]);
            }

            // Vérifier si le colis n'est pas déjà dans un autre bon de ramassage en cours
            $existingPickupSlip = PickupSlip::whereHas('parcels', function ($query) use ($parcel) {
                $query->where('parcels.id', $parcel->id);
            })
            ->where('status', '!=', 'completed')
            ->first();

            if ($existingPickupSlip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce colis est déjà dans le bon de ramassage: ' . $existingPickupSlip->reference
                ]);
            }

            return response()->json([
                'success' => true,
                'parcel' => $parcel,
                'message' => 'Colis trouvé'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche du colis'
            ], 500);
        }
    }

    /**
     * Mise à jour du statut d'un bon de ramassage
     */
    public function updateStatus(Request $request, PickupSlip $pickupSlip)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        try {
            $pickupSlip->update([
                'status' => $request->status
            ]);

            // Si le statut est "completed", mettre à jour le statut des colis
            if ($request->status === 'completed') {
                $pickupSlip->parcels()->update(['status' => 'picked_up']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
    }

    /**
     * Impression du bon de ramassage
     */
    public function print(PickupSlip $pickupSlip)
    {
        $pickupSlip->load(['deliveryCompany', 'parcels', 'user']);
        
        return view('pickup-slips.print', compact('pickupSlip'));
    }

    /**
     * Export des bons de ramassage
     */
    public function export(Request $request)
    {
        $query = PickupSlip::with(['deliveryCompany', 'user'])->withCount('parcels');

        // Filtres optionnels
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('delivery_company_id')) {
            $query->where('delivery_company_id', $request->delivery_company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pickupSlips = $query->orderBy('created_at', 'desc')->get();

        return view('pickup-slips.export', compact('pickupSlips'));
    }

    /**
     * Statistiques des bons de ramassage
     */
    public function statistics()
    {
        $stats = [
            'total' => PickupSlip::count(),
            'pending' => PickupSlip::where('status', 'pending')->count(),
            'in_progress' => PickupSlip::where('status', 'in_progress')->count(),
            'completed' => PickupSlip::where('status', 'completed')->count(),
            'cancelled' => PickupSlip::where('status', 'cancelled')->count(),
            'today' => PickupSlip::whereDate('created_at', today())->count(),
            'this_week' => PickupSlip::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => PickupSlip::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count(),
        ];

        return response()->json($stats);
    }
}