<?php
  
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\DeliveryCompany;
use App\Models\OrderImage;
use App\Models\User;
use App\Models\Product;
use App\Models\Variation;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
  
class OrderController extends Controller
{

  public function __construct()
  {
      $this->middleware('auth');
  }

    /**
     * Affiche la liste des commandes
     */
    public function index()
    {
        $deliveryCompanies = DeliveryCompany::all();
        $users = User::all();
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
        
        return view('orders.index', compact('statusOptions', 'deliveryCompanies', 'users'));
    }

/**
     * Page des archives (commandes avec colis)
     */
    public function current()
    {
        $deliveryCompanies = DeliveryCompany::all();
        $users = User::all();
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

        return view('orders.archives', compact('statusOptions', 'deliveryCompanies', 'users'));
    }

    public function archives()
    {
        $deliveryCompanies = DeliveryCompany::all();
        $users = User::all();
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

        return view('orders.archives', compact('statusOptions', 'deliveryCompanies', 'users'));
    }
    /**
     * API pour récupérer les commandes en cours
     */
    public function getCurrentOrders(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['client', 'deliveryCompany', 'user'])
                ->whereDoesntHave('parcel') // Commandes sans colis
                ->select('orders.*');

            return $this->buildDataTable($orders, $request);
        }
        return abort(404);
    }

    /**
     * API pour récupérer les archives
     */
    public function getArchivedOrders(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['client', 'deliveryCompany', 'user', 'parcel'])
                ->whereHas('parcel') // Commandes avec colis
                ->select('orders.*');

            return $this->buildDataTable($orders, $request, true);
        }
        return abort(404);
    }

    /**
     * Méthode originale - maintenant pour toutes les commandes
     */
    public function getOrders(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['client', 'deliveryCompany', 'user'])->select('orders.*');
            return $this->buildDataTable($orders, $request);
        }
        return abort(404);
    }

    /**
     * Méthode commune pour construire le DataTable
     */
    private function buildDataTable($orders, Request $request, $isArchive = false)
    {
        $dataTable = DataTables::of($orders)
            ->addColumn('client_name', function ($order) {
                $class="";
                if(Parcel::where('order_id',$order->id)->exists()){
                    $class="hidden";
                }
                if ($order->client) {
                    return '<div class="'.$class.'">'.$order->client->full_name . '<br><small>' . $order->client->phone . '</small></div>';
                }
                return '<div class="'.$class.'"><span class="text-muted">Non défini</span></div>';
            })
            ->addColumn('service_type_formatted', function ($order) {
                if ($order->service_type) {
                    return $order->service_type == 'Livraison' ? '<b>Livraison</b>' : '<i>Échange</i>' ;
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('delivery_company_info', function ($order) {
                if ($order->deliveryCompany) {
                    if (isset($order->parcel)) {
                        $result = '<span class="badge bg-'.$order->deliveryCompany->id.'">'.ucfirst($order->deliveryCompany->name).' <a style="color:white" href="' . route('parcels.show', $order->parcel->id) . '"  >'.$order->parcel->reference.'</a></span> ';
                    }else{
                        $result =  '<span class="badge bg-'.$order->deliveryCompany->id.'">'.ucfirst($order->deliveryCompany->name).'</span> ' ;
                    }
                    if ($order->free_delivery) {
                        $result .= ' <span class="badge bg-success">Gratuite</span>';
                    }

                    return $result;
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status_formatted', function ($order) {
                $statusLabels = [
                    'draft' => 'Brouillon',
                    'pending' => 'En attente',
                    'no_stock' => 'Rupture de stock',
                    'production' => 'En production',
                    'confirmed' => 'Confirmée',
                    'no_response' => 'Client ne répond plus',
                    'not_available' => 'Client injoignable',
                    'cancelled' => 'Annulée',
                ];
                $last_comment = OrderStatusHistory::where('order_id', $order->id)
                    ->orderBy('id', 'desc')
                    ->first()
                    ->comment ?? '';
                return '<span class="status-badge status-' . $order->status . '">' . 
                       ($statusLabels[$order->status] ?? $order->status) . 
                       '</span><br><small>'.$last_comment.'</small>';
            })
            ->addColumn('created_at_formatted', function ($order) {
                $createdInfo = $order->created_at->format('d/m/Y H:i');
                if ($order->user) {
                    $createdInfo .= '<br><small>par ' . $order->user->name . '</small>';
                }
                return $createdInfo;
            });

        // Ajouter la colonne parcel pour les archives
        if ($isArchive) {
            $dataTable->addColumn('parcel_info', function ($order) {
                if ($order->parcel) {
                    return '<strong>' . $order->parcel->tracking_number . '</strong><br>' .
                           '<small>Créé le ' . $order->parcel->created_at->format('d/m/Y') . '</small>';
                }
                return '<span class="text-muted">-</span>';
            });
        }

            $dataTable->filter(function ($query) use ($request) {
                // Filtre global (recherche dans toutes les colonnes)
                if ($request->has('search') && !empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('client', function($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        })
                        ->orWhereHas('deliveryCompany', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        // Ajout de la recherche par date formatée
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') LIKE ?", ["%{$search}%"]);
                    });
                }

                // Filtre spécifique par statut
                if ($request->has('status') && !empty($request->status)) {
                    $query->where('status', $request->status);
                }
                
                // Filtre par société de livraison
                if ($request->has('delivery_company') && !empty($request->delivery_company)) {
                    $query->where('delivery_company_id', $request->delivery_company);
                }
                
                // Filtre par utilisateur
                if ($request->has('user_id') && !empty($request->user_id)) {
                    $query->where('user_id', $request->user_id);
                }
                
                // Filtres par colonne individuelle
                if ($request->has('columns')) {
                    foreach ($request->columns as $column) {
                        if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                            $search = $column['search']['value'];
                            switch ($column['name']) {
                                case 'id':
                                    $query->where('id', 'like', "%{$search}%");
                                    break;
                                case 'client_name':
                                    $query->whereHas('client', function($q) use ($search) {
                                        $q->where('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%")
                                        ->orWhere('phone', 'like', "%{$search}%");
                                    });
                                    break;
                                case 'service_type_formatted':
                                    $query->where('service_type', 'like', "%{$search}%");
                                    break;
                                case 'delivery_company_info':
                                    $query->whereHas('deliveryCompany', function($q) use ($search) {
                                        $q->where('name', 'like', "%{$search}%");
                                    });
                                    break;
                                case 'status_formatted':
                                    $query->where('status', 'like', "%{$search}%");
                                    break;
                                case 'created_at': // Ajout du cas pour la recherche par date
                                    $query->where(function($q) use ($search) {
                                        $q->whereRaw("DATE_FORMAT(created_at, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                                        ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') LIKE ?", ["%{$search}%"]);
                                    });
                                    break;
                            }
                        }
                    }
                }

                if ($request->has('date_from') && $request->has('date_to') && !empty($request->date_from) && !empty($request->date_to)) {
                    $from = date('Y-m-d 00:00:00', strtotime($request->date_from));
                    $to = date('Y-m-d 23:59:59', strtotime($request->date_to));
                    $query->whereBetween('created_at', [$from, $to]);
                }
            })

        ->addColumn('action', function ($order) {
                    $buttons = '';
                    //$buttons .= '<a href="' . route('orders.show', $order->id) . '" class="btn btn-sm btn-info mr-1 mb-1" title="Voir"><i class="fas fa-eye"></i></a>';
                    $buttons .= '<a href="' . route('orders.edit', $order->id) . '" class="btn btn-sm btn-primary mr-1 mb-1" title="Modifier"><i class="fas fa-edit"></i></a>';
                    $buttons .= '<form action="' . route('orders.destroy', $order->id) . '" method="POST" style="display:inline;" class="mr-1">';
                    $buttons .= csrf_field();
                    $buttons .= method_field('DELETE');
                    $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1" title="Supprimer" onclick="return confirm(\'Êtes-vous sûr?\')"><i class="fas fa-trash"></i></button>';
                    $buttons .= '</form>';
                    return $buttons;
        });

        $rawColumns = ['client_name', 'service_type_formatted', 'delivery_company_info', 'status_formatted', 'created_at_formatted', 'action'];
        
        if ($isArchive) {
            $rawColumns[] = 'parcel_info';
        }

        return $dataTable->rawColumns($rawColumns)->make(true);
    }

    public function show(Order $order)
    {
         return view('orders.show', compact('order'));
    }

    /**
     * Formulaire de création rapide (brouillon)
     */
    public function create()
    {
        // Récupérer la préférence de redirection de l'utilisateur depuis la session
        $redirectPreference = Session::get('order_redirect_preference', 'create_another');
        
        return view('orders.create', compact('redirectPreference'));
    }

    /**
     * Enregistre une nouvelle commande brouillon
     */
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120',
            'redirect_preference' => 'required|in:create_another,finalize'
        ]);
        
        // Enregistrer la préférence de redirection dans la session
        Session::put('order_redirect_preference', $request->redirect_preference);
        
        DB::beginTransaction();
        try {
            // Créer la commande brouillon
            $order = Order::create([
                'notes' => $request->notes,
                'status' => 'draft',
                'user_id' => Auth::id() // Ajouter l'utilisateur courant
            ]);
            
            // Gérer les images uploadées
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {

                // Générer un nom de fichier unique
                $filename = time() . '_' . $image->getClientOriginalName();
                
                // Stocker l'image directement dans le dossier public
                // Au lieu d'utiliser storage, on utilise le dossier public/uploads qui est toujours accessible
                $uploadPath = 'uploads/orders/' . $order->id;
                $fullPath = public_path($uploadPath);
                /*
                // Créer le répertoire s'il n'existe pas
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }
                */
                // Déplacer le fichier vers le répertoire public
                $image->move($fullPath, $filename);
                
                // Chemin relatif pour l'accès web (important pour l'affichage)
                $path = $uploadPath . '/' . $filename;
 
                    OrderImage::create([
                        'order_id' => $order->id,
                        'path' => $path
                    ]);
                }
            }
            
            // Enregistrer l'historique du statut initial
            $order->addStatusHistory(null, 'draft');
            
            DB::commit();
            
            // Pour les requêtes AJAX (Dropzone)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $request->redirect_preference === 'create_another' 
                        ? route('orders.create') 
                        : route('orders.edit', $order)
                ]);
            }
            
            // Pour les requêtes normales
            if ($request->redirect_preference === 'create_another') {
                return redirect()->route('orders.create')
                    ->with('success', 'Commande brouillon créée avec succès');
            } else {
                return redirect()->route('orders.edit', $order)
                    ->with('success', 'Commande brouillon créée, vous pouvez maintenant la finaliser');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Erreur lors de la création de la commande: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire d'édition d'une commande
     */
    public function edit(Order $order)
    {
        $deliveryCompanies = DeliveryCompany::all();
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
        
        $serviceTypes = [
            'Livraison' => 'Livraison',
            'Echange' => 'Échange'
        ];
        
        // Liste des délégations de Tunisie (à compléter selon vos besoins)
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

        $products = Product::with([
            'variations' => function ($query) {
                $query->with('attributeValues.attribute');
            }
        ])->get();

        $items= $order->items()->get();
        
        return view('orders.edit', compact('order', 'deliveryCompanies', 'statusOptions', 'serviceTypes', 'delegations','items','products'));
    }

    /**
     * Mise à jour d'une commande
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'phone' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'delegation' => 'required|string|max:255',
            'address' => 'required|string',
            'phone2' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'service_type' => 'required|in:Livraison,Echange',
            'delivery_company_id' => 'nullable|exists:delivery_companies,id',
            'free_delivery' => 'nullable|boolean',
            'status' => 'required|in:draft,pending,production,no_response,no_stock,cancelled,confirmed,not_available',
            'status_comment' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            // Gestion du client
            $client = null;
            if ($request->has('client_id') && $request->client_id) {
                // Client existant, mise à jour
                $client = Client::findOrFail($request->client_id);
                $client->update([
                    'phone' => $request->phone,
                    'phone2' => $request->phone2,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'city' => $request->city,
                    'delegation' => $request->delegation,
                    'address' => $request->address,
                    'postal_code' => $request->postal_code,
                ]);
            } else {
                // Nouveau client
                $client = Client::create([
                    'phone' => $request->phone,
                    'phone2' => $request->phone2,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'city' => $request->city,
                    'delegation' => $request->delegation,
                    'address' => $request->address,
                    'postal_code' => $request->postal_code,
                ]);
            }
            

            // Calculer le total des produits et les frais de livraison
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $deliveryCost = 0;
            if (!$request->has('free_delivery') && $request->delivery_company_id) {
                $deliveryCompany = DeliveryCompany::find($request->delivery_company_id);
                if ($deliveryCompany) {
                    $deliveryCost = $deliveryCompany->delivery_price;
                }
            }

            $discount = $request->discount ?? 0;
            $total = $subtotal - $discount + $deliveryCost;

            // Si le statut a changé, enregistrer dans l'historique
            $oldStatus = $order->status;
            if ($oldStatus !== $request->status) {
                $order->addStatusHistory($oldStatus, $request->status, $request->status_comment);
            }
            
            // Mise à jour de la commande
            $order->update([
                'client_id' => $client->id,
                'service_type' => $request->service_type,
                'delivery_company_id' => $request->delivery_company_id,
                'free_delivery' => $request->has('free_delivery'),
                'status' => $request->status,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_cost' => $deliveryCost,
                'total' => $total,
            ]);

            /*
            // Restaurer les stocks des produits actuels avant de les supprimer
            foreach ($order->items as $item) {
                $this->restoreStockQuantity($item);
            }
            */
            // Supprimer les éléments actuels de la commande
            $order->items()->delete();
            
            // Ajouter les nouveaux éléments
            foreach ($request->items as $itemData) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'variation_id' => $itemData['variation_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
                ]);
            
                // Déduire du stock
                //$this->deductFromStock($orderItem);
            }            
            
            DB::commit();
            
            return redirect()->route('orders.index')
                ->with('success', 'Commande mise à jour avec succès');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour de la commande: ' . $e->getMessage());
        }
    }


    public function updateNotes(Request $request)
    {
        $order =  Order::find($request->order);
        
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $order->update([
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notes mises à jour avec succès'
        ]);
    }

    /**
     * Supprime une commande
     */
    public function destroy(Order $order)
    {
        try {
            // Supprimer les images associées
            foreach ($order->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            
            $order->delete();
            
            return redirect()->route('orders.index')
                ->with('success', 'Commande supprimée avec succès');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }
    }
    
    /**
     * Supprimer une image de commande spécifique
     */
    public function deleteImage($id)
    {
        $image = OrderImage::findOrFail($id);
        
        try {
            Storage::disk('public')->delete($image->path);
            $image->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }




}