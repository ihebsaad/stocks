<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\DeliveryCompany;
use App\Models\PickupSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickupSlipController extends Controller
{
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
        
        return view('pickup-slips.show', compact('pickupSlip'));
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
}