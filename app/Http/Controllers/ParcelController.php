<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\Product;
use App\Models\Variation;
use App\Models\OrderItem;
use App\Models\DeliveryCompany;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS1D;


class ParcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $deliveryCompanies = DeliveryCompany::all();
        return view('parcels.index', compact('deliveryCompanies'));
    }

   public function store(Order $order)
    {
        $client = $order->client;
        $deliveryCompany = $order->deliveryCompany;

        if (!$deliveryCompany) {
            return back()->with('error', 'Aucune société de livraison sélectionnée.');
        }

        // Vérifier le stock AVANT de créer le colis
        try {
            $this->checkStockAvailability($order);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
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
            'cod' => $order->total,
            'libelle' => $order->items->first()->product->name ?? 'Commande',
            'nb_piece' => $order->items->count(),
            'remarque' => $order->notes,
            'service' => $order->service_type,
            'ville_cl' => $client->city ?? '',
        ]);

        if($parcel->id > 0) {
            // Envoyer à l'API
            $deliveryService = new DeliveryService($deliveryCompany);
            $apiResponse = $deliveryService->createParcel($parcel->toArray());

            if (isset($apiResponse['reference'])) {
                // Mise à jour du statut
                $parcel->update([
                    'status' => 'envoyé',
                    'reference' => $apiResponse['reference'],
                    'tracking_url' => $apiResponse['url'] ?? null,
                    'api_message' => $apiResponse['message'] ?? null,
                ]);

                // Déduire du stock SEULEMENT après succès de l'API
                try {
                    foreach ($parcel->order->items as $item) {
                        $this->deductFromStock($item);
                    }
                } catch (\Exception $e) {
                    // Si erreur lors de la déduction, on peut restaurer ou gérer l'erreur
                    return back()->with('error', 'Erreur lors de la déduction du stock: ' . $e->getMessage());
                }

                return back()->with('success', 'Colis créé et envoyé avec succès. Réf: ' . $parcel->reference);
            } else {
                return back()->with('error', 'Erreur API: ' . json_encode($apiResponse));
            }
        } else {
            return back()->with('error', 'Erreur lors de la création du colis');
        }
    }

    /**
     * Vérifier la disponibilité du stock avant traitement
     */
    private function checkStockAvailability(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->variation_id) {
                $variation = Variation::find($item->variation_id);
                if (!$variation) {
                    throw new \Exception("Variation introuvable pour l'article: {$item->product->name}");
                }
                if ($variation->stock_quantity < $item->quantity) {
                    throw new \Exception("Stock insuffisant pour la variation {$variation->reference}. Stock disponible: {$variation->stock_quantity}, Quantité demandée: {$item->quantity}");
                }
            } else {
                $product = Product::find($item->product_id);
                if (!$product) {
                    throw new \Exception("Produit introuvable pour l'article ID: {$item->product_id}");
                }
                if ($product->stock_quantity < $item->quantity) {
                    throw new \Exception("Stock insuffisant pour le produit {$product->name}. Stock disponible: {$product->stock_quantity}, Quantité demandée: {$item->quantity}");
                }
            }
        }
    }

    /**
     * Déduire la quantité du stock
     */
    private function deductFromStock(OrderItem $item)
    {
        if ($item->variation_id) {
            $variation = Variation::findOrFail($item->variation_id);
            $variation->stock_quantity -= $item->quantity;
            $variation->save();
        } else {
            $product = Product::findOrFail($item->product_id);
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
                ->addColumn('checkbox', function ($parcel) {
                    return '<input type="checkbox" class="parcel-checkbox" value="' . $parcel->id . '">';
                })            
                ->addColumn('reference', function ($parcel) {
                    return '<a href="' . route('parcels.show', $parcel->id) . '">'.'#'.$parcel->id.'<br>'.$parcel->reference ?? '<span class="text-muted">#'.$parcel->id.'</span>'.'</a>' ;
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
                ->addColumn('delivery_company', function ($parcel) {
                    if ($parcel->company) {
                        $result =  '<span class="badge mr-2 bg-'.$parcel->company->id.'">'.ucfirst($parcel->company->name).'</span>' ;
                        if ($parcel->service && $parcel->service!='Livraison') {
                            $result.='<span class="badge bg-danger"><i class="fas fa-exchange-alt"></i> Échange</span>'; 
                        } 
                        return $result;
                    }
                })

                ->addColumn('order_id', function ($parcel) {
                    return '<a target="_blank" href="'.route('orders.edit', $parcel->order_id).'">#'.$parcel->order_id.' </a><br>'.$parcel->cod.'<sup>TND</sup>'  ;
                })
                ->addColumn('dernier_etat', function ($parcel) {
                    return $parcel->dernier_etat ? $parcel->dernier_etat . '<br><small>' . $parcel->date_dernier_etat . '</small>' : '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($parcel) {
                    $buttons = '';
                    $buttons .= '<a href="' . route('parcel.bl', $parcel->id) . '" class="btn btn-sm btn-success mr-1 mb-1" title="BL"  target="_blank" ><i class="fas fa-file-pdf"></i></a>';
                    $buttons .= '<a href="' . route('parcels.edit', $parcel->id) . '" class="btn btn-sm btn-primary mr-1 mb-1" title="Modifier"><i class="fas fa-edit"></i></a>';
                    $buttons .= '<form action="' . route('parcels.destroy', $parcel->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Confirmer la suppression ?\')">';
                    $buttons .= csrf_field();
                    $buttons .= method_field('DELETE');
                    $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1" title="Supprimer"><i class="fas fa-trash"></i></button>';
                    $buttons .= '</form>';
                    return $buttons;
                })
                ->rawColumns(['checkbox','reference', 'client', 'status', 'delivery_company','service_type', 'dernier_etat','order_id', 'action'])
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
                            })
                            ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') LIKE ?", ["%{$search}%"]);
                        });
                    }

                    if ($request->has('status') && !empty($request->status)) {
                        $query->where('status', $request->status);
                    }

                    if ($request->has('delivery_company_id') && !empty($request->delivery_company_id)) {
                        $query->where('delivery_company_id', $request->delivery_company_id);
                    }
                    
                    if ($request->has('date_from') && $request->has('date_to') && !empty($request->date_from) && !empty($request->date_to)) {
                        $from = date('Y-m-d 00:00:00', strtotime($request->date_from));
                        $to = date('Y-m-d 23:59:59', strtotime($request->date_to));
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                })
                ->make(true);
        }
    }
     

    public function generateParcelsPdf(Request $request)
    {
        try {
            $parcelIds = $request->input('parcel_ids', []);
            
            if (empty($parcelIds)) {
                return back()->with('error', 'Aucun colis sélectionné');
            }

            $parcels = Parcel::with(['order.client', 'company'])
                ->whereIn('id', $parcelIds)
                ->get();

            if ($parcels->isEmpty()) {
                return back()->with('error', 'Aucun colis trouvé');
            }

            // Générer les codes à barres pour chaque colis
            $generator = new \Milon\Barcode\DNS1D();
            $barcodes = [];
            
            foreach ($parcels as $parcel) {
                $reference = $parcel->reference ?: '#' . $parcel->id;
                $barcodes[$parcel->id] = $generator->getBarcodeHTML($reference, 'C128', 1.2, 30);
            }

            $data = [
                'parcels' => $parcels,
                'barcodes' => $barcodes,
                'generated_at' => now()->format('d/m/Y H:i'),
                'total_count' => $parcels->count()
            ];

            $pdf = Pdf::loadView('parcels.pdf-list', $data);
            
            return $pdf->download('liste_colis_' . date('Y-m-d_H-i-s') . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Erreur génération PDF: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }


    public function generateBL($parcelId)
    {
        // Récupérer le colis avec ses relations
        $parcel = Parcel::with(['company', 'order.items.product'])->findOrFail($parcelId);
        
        // Données de l'expéditeur
        $expediteur = [
            'nom' => 'Z&A Home',
            'adresse' => 'Ksibet médiouni',
            'telephone' => '55 969 997',
            'mf' => '1768373/Z/P/M/000'
        ];
        
        $barcode = $this->generateBarcodeWithMilon($parcel->reference);

        // Générer le PDF
        $pdf = Pdf::loadView('bl.template', compact('parcel', 'expediteur','barcode'));
        
        // Configurer le PDF pour format ticket (A5)
        $pdf->setPaper('A5', 'portrait');
        
        // Retourner le PDF
        return $pdf->stream('BL-' . $parcel->reference . '.pdf');
    }
  

        private function generateBarcodeWithMilon($reference)
    {
        // Si vous voulez utiliser milon/barcode:
        // composer require milon/barcode
        
        if (class_exists('\Milon\Barcode\DNS1D')) {
            $generator = new \Milon\Barcode\DNS1D();
            return $generator->getBarcodeHTML($reference, 'C128', 1.5, 35);
        }
        
        return $this->generateSimpleBarcode($reference);
    }

    public function show(Parcel $parcel)
    {
        $client = $parcel->order->client;
        $statusOptions = [
            'draft' => 'Brouillon',
            'pending' => 'En attente',
            'no_stock' => 'Rupture de stock',
            'production' => 'En production',
            'confirmed' => 'Confirmée',
            'no_response' => 'Client ne répond plus',
            'not_available' => 'Client injoignable',
            'cancelled' => 'Annulée',
        ];

        $historiques= $parcel->order->statusHistory->sortByDesc('created_at');
        return view('parcels.show', compact('parcel','client','historiques','statusOptions'));

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
    
}
