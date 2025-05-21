<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\DeliveryCompany;
use App\Models\OrderStatusHistory;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ParcelController extends Controller
{

    public function index()
    {
        return view('parcels.index');
    }

    public function store(Order $order)
    {
        $client = $order->client;
        $deliveryCompany = $order->deliveryCompany;

        if (!$deliveryCompany) {
            return back()->with('error', 'Aucune société de livraison sélectionnée.');
        }

        // Créer l'enregistrement local Parcel
        $parcel = Parcel::create([
            'order_id' => $order->id,
            'delivery_company_id' => $deliveryCompany->id,
            'tel_l' => $client->phone,
            'tel2_l' => $client->phone2,
            'nom_client' => $client->first_name . ' ' . $client->last_name,
            'gov_l' => $client->delegation,
            'adresse_l' => $client->city . ' '.$client->address. ' '.$client->postal_code,
            'cod' => 1,//$order->total,
            'libelle' => $order->items->first()->product->name ?? 'Commande',
            'nb_piece' => $order->items->count(),
            'remarque' => $order->notes,
            'service' => $order->service_type,
        ]);

        // Envoyer à l'API
        $deliveryService = new DeliveryService($deliveryCompany);
        $apiResponse = $deliveryService->createParcel($parcel->toArray());


        if (isset($apiResponse['reference'])) {

            // mise a jour statut ici

            $parcel->update([
                'status' => 'envoyé',
                'reference' => $apiResponse['reference'],
                'tracking_url' => $apiResponse['url'] ?? null,
                'api_message' => $apiResponse['message'] ?? null,
            ]);

            return back()->with('success', 'Colis créé et envoyé avec succès. Réf: ' . $parcel->reference);
        } else {
            return back()->with('error', 'Erreur API: ' . json_encode($apiResponse));
        }

    }


    public function getParcels(Request $request)
    {
        if ($request->ajax()) {
            $parcels = Parcel::with(['order.client', 'company'])->select('parcels.*')->orderBy('parcels.id','desc');

            return DataTables::of($parcels)
                ->addColumn('reference', function ($parcel) {
                    return $parcel->reference ?? '<span class="text-muted">#'.$parcel->id.'</span>';
                })
                ->addColumn('created_at_formatted', function ($parcel) {
                    $createdInfo = $parcel->created_at->format('d/m/Y H:i');
                    
                    return $createdInfo;
                })
                ->addColumn('client', function ($parcel) {
                    if ($parcel->order && $parcel->order->client) {
                        $client = $parcel->order->client;
                        return $client->full_name . '<br><small>' . $client->phone . '</small>';
                    }
                    return '<span class="text-muted">Non défini</span>';
                })
                ->addColumn('status', function ($parcel) {
                    if($parcel->dernier_etat!='')
                        return '<span class="badge bg-info">' . ($parcel->dernier_etat   .' ('.$parcel->date_dernier_etat->format('d/m/Y H:i').') ' ?? '-') . '</span>';

                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('delivery_company', function ($parcel) {
                    return $parcel->company ? $parcel->company->name : '<span class="text-muted">-</span>';
                })
                ->addColumn('service_type', function ($parcel) {
                    if ($parcel->service) {
                        return $parcel->service == 'delivery' ? 'Livraison' : 'Échange' ;
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('order_id', function ($parcel) {
                    return '<a target="_blank" href="'.route('orders.edit', $parcel->order_id).'">#'.$parcel->order_id.' </a>'  ;
                })
                ->addColumn('dernier_etat', function ($parcel) {
                    return $parcel->dernier_etat ? $parcel->dernier_etat . '<br><small>' . $parcel->date_dernier_etat . '</small>' : '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($parcel) {
                    $buttons = '';
                    $buttons .= '<a href="' . route('parcels.edit', $parcel->id) . '" class="btn btn-sm btn-primary mr-1 mb-1" title="Modifier"><i class="fas fa-edit"></i></a>';
                    $buttons .= '<form action="' . route('parcels.destroy', $parcel->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Confirmer la suppression ?\')">';
                    $buttons .= csrf_field();
                    $buttons .= method_field('DELETE');
                    $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1" title="Supprimer"><i class="fas fa-trash"></i></button>';
                    $buttons .= '</form>';
                    return $buttons;
                })
                ->rawColumns(['reference', 'client', 'status', 'delivery_company', 'dernier_etat','order_id', 'action'])
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('reference', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('order.client', function($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            })
                            ->orWhereHas('company', function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                        });
                    }

                    if ($request->has('status') && !empty($request->status)) {
                        $query->where('status', $request->status);
                    }

                    if ($request->has('delivery_company_id') && !empty($request->delivery_company_id)) {
                        $query->where('delivery_company_id', $request->delivery_company_id);
                    }
                })
                ->make(true);
        }
    }
     

 
    
    public function show(Parcel $parcel)
    {

    }

    
    public function update(Parcel $parcel)
    {

    }

    
    public function edit(Parcel $parcel)
    {

    }

    
    public function destroy(Parcel $parcel)
    {

    }
}
