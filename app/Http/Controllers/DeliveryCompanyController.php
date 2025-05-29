<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeliveryCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la liste des sociétés de livraison
     */
    public function index()
    {
        return view('delivery_companies.index');
    }

    /**
     * Obtenir les données des sociétés de livraison pour DataTables
     */
    public function getDeliveryCompanies(Request $request)
    {
        if ($request->ajax()) {
            $deliveryCompanies = DeliveryCompany::select('delivery_companies.*');

            return DataTables::of($deliveryCompanies)
                ->addColumn('delivery_price_formatted', function ($company) {
                    return $company->formatted_price;
                })
                ->addColumn('created_at_formatted', function ($company) {
                    return $company->created_at->format('d/m/Y H:i');
                })
                ->addColumn('action', function ($company) {
                    $buttons = '';
                    $buttons .= '<a href="' . route('delivery-companies.edit', $company->id) . '" class="btn btn-sm btn-primary mr-1 mb-1" title="Modifier"><i class="fas fa-edit"></i></a>';
                    $buttons .= '<form action="' . route('delivery-companies.destroy', $company->id) . '" method="POST" style="display:inline;" class="mr-1">';
                    $buttons .= csrf_field();
                    $buttons .= method_field('DELETE');
                    $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1" title="Supprimer" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette société de livraison?\')"><i class="fas fa-trash"></i></button>';
                    $buttons .= '</form>';
                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return abort(404);
    }

    /**
     * Affiche le formulaire de création d'une société de livraison
     */
    public function create()
    {
        return view('delivery_companies.create');
    }

    /**
     * Enregistre une nouvelle société de livraison
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_companies',
            'delivery_price' => 'required|numeric|min:0',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            DeliveryCompany::create([
                'name' => $request->name,
                'delivery_price' => $request->delivery_price,
                'manager_name' => $request->manager_name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            DB::commit();

            return redirect()->route('delivery-companies.index')
                ->with('success', 'Société de livraison créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la société de livraison: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire d'édition d'une société de livraison
     */
    public function edit(DeliveryCompany $deliveryCompany)
    {
        return view('delivery_companies.edit', compact('deliveryCompany'));
    }

    /**
     * Mise à jour d'une société de livraison
     */

    public function update(Request $request, DeliveryCompany $deliveryCompany)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_companies,name,' . $deliveryCompany->id,
            'delivery_price' => 'required|numeric|min:0',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'api_url_prod' => 'nullable|url|max:500',
            'api_url_dev' => 'nullable|url|max:500',
            'code_api' => 'nullable|string|max:100',
            'cle_api' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $deliveryCompany->update([
                'name' => $request->name,
                'delivery_price' => $request->delivery_price,
                'manager_name' => $request->manager_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'api_url_prod' => $request->api_url_prod,
                'api_url_dev' => $request->api_url_dev,
                'code_api' => $request->code_api,
                'cle_api' => $request->cle_api,
            ]);

            DB::commit();

            return redirect()->route('delivery-companies.index')
                ->with('success', 'Société de livraison mise à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour de la société de livraison: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une société de livraison
     */
    public function destroy(DeliveryCompany $deliveryCompany)
    {
        try {
            // Vérifier si la société de livraison est utilisée
            $ordersCount = $deliveryCompany->orders()->count();
            if ($ordersCount > 0) {
                return back()->with('error', 'Impossible de supprimer cette société de livraison car elle est utilisée par ' . $ordersCount . ' commande(s)');
            }

            $deliveryCompany->delete();

            return redirect()->route('delivery-companies.index')
                ->with('success', 'Société de livraison supprimée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression de la société de livraison: ' . $e->getMessage());
        }
    }
}