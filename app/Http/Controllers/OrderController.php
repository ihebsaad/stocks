<?php
  
  namespace App\Http\Controllers;

  use App\Models\Order;
  use App\Models\Client;
  use App\Models\DeliveryCompany;
  use App\Models\OrderImage;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\Session;
  
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
          $orders = Order::with('client')->latest()->paginate(15);
          return view('orders.index', compact('orders'));
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
                'status' => 'draft'
            ]);
            
            // Gérer les images uploadées
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Store the image with a consistent naming pattern
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $path = $image->storeAs('orders/'.$order->id, $filename, 'public');
                    
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
              'pickup' => 'En ramassage',
              'no_response' => 'Client ne répond plus',
              'cancelled' => 'Annulée',
              'in_delivery' => 'En livraison',
              'completed' => 'Terminée'
          ];
          
          $serviceTypes = [
              'delivery' => 'Livraison',
              'exchange' => 'Échange'
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
          
          return view('orders.edit', compact('order', 'deliveryCompanies', 'statusOptions', 'serviceTypes', 'delegations'));
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
              'last_name' => 'required|string|max:255',
              'city' => 'required|string|max:255',
              'delegation' => 'required|string|max:255',
              'address' => 'required|string',
              'phone2' => 'nullable|string',
              'postal_code' => 'nullable|string|max:10',
              'service_type' => 'required|in:delivery,exchange',
              'delivery_company_id' => 'nullable|exists:delivery_companies,id',
              'free_delivery' => 'nullable|boolean',
              'status' => 'required|in:draft,pending,pickup,no_response,cancelled,in_delivery,completed',
              'status_comment' => 'nullable|string',
              'notes' => 'nullable|string',
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
              ]);
              
              DB::commit();
              
              return redirect()->route('orders.index')
                  ->with('success', 'Commande mise à jour avec succès');
                  
          } catch (\Exception $e) {
              DB::rollBack();
              return back()->with('error', 'Erreur lors de la mise à jour de la commande: ' . $e->getMessage());
          }
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