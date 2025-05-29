<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\Product;
use App\Models\Variation;
use App\Models\OrderItem;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ParcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('parcels.index');
    }

    public function save(Order $order)
    {
        $client = $order->client;
        $deliveryCompany = $order->deliveryCompany;

        if (!$deliveryCompany) {
            return back()->with('error', 'Aucune société de livraison sélectionnée.');
        }
/*
        if (Parcel::where('order_id',$order->id)->exists()) {
            return back()->with('error', 'Colis existe déja !');
        }
*/
        // Créer l'enregistrement local Parcel
        $parcel = Parcel::create([
            'order_id' => $order->id,
            'delivery_company_id' => $deliveryCompany->id,
            'tel_l' => $client->phone,
            'tel2_l' => $client->phone2,
            'nom_client' => $client->first_name . ' ' . $client->last_name,
            'gov_l' => $client->delegation,
            'adresse_l' => $client->city . ' '.$client->address. ' '.$client->postal_code,
            'cod' => $order->total,
            'libelle' => $order->items->first()->product->name ?? 'Commande',
            'nb_piece' => $order->items->count(),
            'remarque' => $order->notes,
            'service' => $order->service_type,
            'ville_cl' => $client->city ?? '',
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

            foreach ($parcel->order->items as $item) {
                $this->deductFromStock($item);
            }

            return back()->with('success', 'Colis créé et envoyé avec succès. Réf: ' . $parcel->reference);
        } else {
            return back()->with('error', 'Erreur API: ' . json_encode($apiResponse));
        }

    }
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && strtolower($d->format($format)) === strtolower($date);
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
                    //$createdInfo=$parcel->created_at;
                    //if($this->validateDate($parcel->created_at))
                    $createdInfo = $parcel->created_at->format('d/m/Y H:i') ?? '';
                    
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
                    if($this->validateDate($parcel->date_dernier_etat))
                        return '<span class="badge bg-info">' . ($parcel->dernier_etat   .' ('.$parcel->date_dernier_etat->format('d/m/Y H:i').') ' ?? '-') . '</span>';

                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('delivery_company', function ($order) {
                    if ($order->deliveryCompany) {
                        $result =  '<span class="badge bg-'.$order->deliveryCompany->id.'">'.ucfirst($order->deliveryCompany->name).'</span> ' ;
                        if ($order->free_delivery) {
                            $result .= ' <span class="badge bg-success">Gratuite</span>';
                        }
                        return $result;
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('service_type', function ($parcel) {
                    if ($parcel->service) {
                        return $parcel->service == 'Livraison' ? 'Livraison' : 'Échange' ;
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
     

 
        
    public function store(Parcel $parcel)
    {

    }
    public function show(Parcel $parcel)
    {

    }

    public function edit(Parcel $parcel)
    {
        $delegations = [
            'Tunis',
            'Ariana',
            'Ben Arous',
            'Manouba',
            'Nabeul',
            'Zaghouan',
            'Bizerte',
            'Béja',
            'Jendouba',
            'Le Kef',
            'Siliana',
            'Sousse',
            'Monastir',
            'Mahdia',
            'Sfax',
            'Kairouan',
            'Kasserine',
            'Sidi Bouzid',
            'Gabès',
            'Medenine',
            'Tataouine',
            'Gafsa',
            'Tozeur',
            'Kebili'
        ];
        return view('parcels.edit', compact('parcel','delegations'));
    }

    public function update(Request $request, Parcel $parcel)
    {
        $validated = $request->validate([
           'tel_l' => 'required',
           'tel2_l' => 'nullable',
           'nom_client' => 'required',
            'gov_l' => 'required',
            'adresse_l' => 'required',
            'cod' => 'required',
            'libelle' => 'nullable',
            'nb_piece' => 'nullable',
            'remarque' => 'nullable',
            'service' => 'required',
        ]);


        $deliveryService = new DeliveryService($parcel->company);
        $response = $deliveryService->updateParcel(array_merge($validated, [
            'code_barre' => $parcel->reference,
        ]));

        if (isset($response['message'])) {
            $parcel->update($validated);
            return redirect()->route('parcels.index')->with('success', 'Colis modifié avec succès.');
        } else {
            return back()->with('error', 'Erreur lors de la mise à jour : ' . json_encode($response));
        }

            
    }
   
    public function destroy(Parcel $parcel)
    {
        $deliveryCompany = $parcel->company;
        $deliveryService = new DeliveryService($deliveryCompany);

        $response = $deliveryService->deleteParcel($parcel->reference);

        if (isset($response['message'])) {

            foreach ($parcel->order->items as $item) {
                $this->restoreStockQuantity($item);
            }

            $parcel->delete();

            return back()->with('success', 'Colis supprimé avec succès.');
        } else {
            return back()->with('error', 'Erreur lors de la suppression: ' . json_encode($response));
        }
    }


    
    /**
     * Déduire la quantité du stock
     */
    private function deductFromStock(OrderItem $item)
    {
        if ($item->variation_id) {
            $variation = Variation::findOrFail($item->variation_id);
            if ($variation->stock_quantity < $item->quantity) {
                throw new \Exception("Stock insuffisant pour la variation {$variation->reference}");
            }
            $variation->stock_quantity -= $item->quantity;
            $variation->save();
        } else {
            $product = Product::findOrFail($item->product_id);
            if ($product->stock_quantity < $item->quantity) {
                throw new \Exception("Stock insuffisant pour le produit {$product->name}");
            }
            $product->stock_quantity -= $item->quantity;
            $product->save();
        }
    }

    /**
     * Restaurer la quantité au stock
     */
    private function restoreStockQuantity(OrderItem $item)
    {
        if ($item->variation_id) {
            $variation = Variation::find($item->variation_id);
            if ($variation) {
                $variation->stock_quantity += $item->quantity;
                $variation->save();
            }
        } else {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock_quantity += $item->quantity;
                $product->save();
            }
        }
    }
}
