<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variation;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StockEntryController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    } 
    
    /**
     * Afficher la page d'entrée de stock
     */
    public function create()
    {
        $products = Product::with([
            'variations' => function ($query) {
                $query->with('attributeValues.attribute');
            }
        ])->get();

        return view('stock.entry', compact('products'));
    }

    /**
     * Enregistrer une entrée de stock
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.prix_achat' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Créer l'entrée de stock principale
            $stockEntry = StockEntry::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
            ]);

            // Ajouter les éléments d'entrée
            foreach ($request->items as $item) {
                $stockEntryItem = StockEntryItem::create([
                    'stock_entry_id' => $stockEntry->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'prix_achat' => $item['prix_achat'],
                ]);

                // Mettre à jour le stock
                if (!empty($item['variation_id'])) {
                    // Produit variable
                    $variation = Variation::find($item['variation_id']);
                    $variation->stock_quantity += $item['quantity'];
                    $variation->save();
                } else {
                    // Produit simple
                    $product = Product::find($item['product_id']);
                    $product->stock_quantity += $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();
            return redirect()->route('stock.entries.index')
                ->with('success', 'Entrée de stock enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()]);
        }
    }

    public function index()
        {
            return view('stock.index');
        }

        public function getStockEntriesList(Request $request)
        {
            if ($request->ajax()) {
                $entries = StockEntry::with('items.product', 'items.variation')
                    ->select('stock_entries.*');

                return DataTables::of($entries)
                    ->addColumn('date_formatted', function ($entry) {
                        return $entry->date->format('d/m/Y');
                    })
                    ->addColumn('products_count', function ($entry) {
                        return $entry->items->count();
                    })
                    ->addColumn('total_formatted', function ($entry) {
                        return number_format($entry->getTotal(), 2, ',', ' ') . ' Dt';
                    })
                    ->addColumn('action', function ($entry) {
                        return '<a href="' . route('stock.entries.show', $entry->id) . '" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }
    /**
     * Afficher les détails d'une entrée de stock
     */
    public function show(StockEntry $entry)
    {
        $entry->load('items.product', 'items.variation.attributeValues.attribute');
        return view('stock.show', compact('entry'));
    }


    /**
     * Mettre à jour la description d'une entrée de stock
     */
    public function updateDescription(Request $request, StockEntry $entry)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
        ]);

        $entry->description = $request->description;
        $entry->save();

        return response()->json([
            'success' => true,
            'message' => 'Description mise à jour avec succès',
        ]);
    }

    /**
     * Calculer les totaux d'une entrée de stock
     */
    public function calculateTotals(StockEntryItem $item)
    {
        // Calculer le sous-total pour cet élément
        $subtotal = $item->quantity * $item->prix_achat;

        // Récupérer l'entrée de stock
        $entry = $item->stockEntry;

        // Calculer le total général
        $total = $entry->getTotal();

        return response()->json([
            'success' => true,
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }

 


    public function update(Request $request, StockEntryItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'prix_achat' => 'required|numeric|min:0',
        ]);

        $item->quantity = $request->quantity;
        $item->prix_achat = $request->prix_achat;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Données mises à jour avec succès',
        ]);
    }
}