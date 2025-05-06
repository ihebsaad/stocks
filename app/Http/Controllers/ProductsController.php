<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockEntryItem;
use App\Models\Variation;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Categorie;
use App\Models\Provider;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ProductsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    } 

    /**
     * Afficher la liste des produits
     */
    public function index()
    {
        //$products = Product::with(['categorie', 'supplier'])->get();
        return view('products.index');
    }


    public function getProducts(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['provider', 'categorie'])->select('products.*');

            return DataTables::of($products)
                ->addColumn('reference', function ($product) {
                    return $product->reference;
                })
                ->addColumn('provider_name', function ($product) {
                    return $product->provider->company ?? 'N/A';
                })
                ->addColumn('category_name', function ($product) {
                    return $product->categorie->name ?? 'N/A';
                })
                ->addColumn('prix_ttc', function ($product) {
                    return $product->prix_ttc;
                })
                ->addColumn('name', function ($product) {
                    return $product->name;
                })
                ->addColumn('type', function ($product) {
                    if($product->type==1){
                        $variations= Variation::where('product_id', $product->id)->count();
                        return 'Var ('.$variations.')';
                    }else{
                        return 'Simp';
                    }
                })
                ->addColumn('stock_quantity', function ($product) {
                    $qty = StockEntryItem::where('product_id', $product->id)->sum('quantity');
                    $qty += $product->stock_quantity;
                    
                    return $qty;
                })
                ->filterColumn('stock_quantity', function($query, $keyword) {
                    $query->whereRaw("(stock_quantity + (SELECT COALESCE(SUM(quantity), 0) FROM stock_entry_items WHERE stock_entry_items.product_id = products.id)) like ?", ["%{$keyword}%"]);
                })
                ->setRowData(['min_qty' => function($product) { return $product->min_qty; }]) // Pour accéder à min_qty en JS
                ->setRowAttr(['data-min-qty' => function($product) { return $product->min_qty; }]) // Alternative
                ->editColumn('description', function ($product) {
                    return nl2br($product->description);
                })
                // Filtres optimisés
                ->filter(function ($query) use ($request) {
                    // Filtre global (recherche dans toutes les colonnes)
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('reference', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('prix_ttc', 'like', "%{$search}%")
                            ->orWhere('stock_quantity', 'like', "%{$search}%")
                            ->orWhereHas('provider', function($q) use ($search) {
                                $q->where('company', 'like', "%{$search}%");
                            })
                            ->orWhereHas('categorie', function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                        });
                    }

                    // Filtres par colonne individuelle
                    if ($request->has('columns')) {
                        foreach ($request->columns as $column) {
                            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                                $search = $column['search']['value'];
                                switch ($column['name']) {
                                    case 'reference':
                                        $query->where('reference', 'like', "%{$search}%");
                                        break;
                                    case 'name':
                                        $query->where('name', 'like', "%{$search}%");
                                        break;
                                    case 'category_name':
                                        $query->whereHas('categorie', function($q) use ($search) {
                                            $q->where('name', 'like', "%{$search}%");
                                        });
                                        break;
                                    case 'provider_name':
                                        $query->whereHas('provider', function($q) use ($search) {
                                            $q->where('company', 'like', "%{$search}%");
                                        });
                                        break;
                                    case 'prix_ttc':
                                        $query->where('prix_ttc', 'like', "%{$search}%");
                                        break;
                                    case 'stock_quantity':
                                        $query->where('stock_quantity', 'like', "%{$search}%");
                                        break;
                                }
                            }
                        }
                    }
                })
                ->addColumn('action', function ($product) {
                    $buttons = '';
                    $buttons .= '<a class="btn btn-sm btn-secondary mr-2 mb-1" href="' . route('products.show', $product->id) . '"><i class="fas fa-eye"></i></a>';
                    $buttons .= '<a class="btn btn-sm btn-primary mr-2 mb-1" href="' . route('products.edit', $product->id) . '"><i class="fas fa-pen"></i></a>';
                    if($product->type==0){
                        $buttons .= '<form action="'. route('products.duplicate', $product->id) .'" method="POST" style="display:inline;" class="mr-2 ml-2">';
                        $buttons .= csrf_field();
                    
                        $buttons .= '<button title="Dupliquer" type="submit" class="btn btn-secondary mb-1 btn-sm"><i class="fas fa-copy"></i></button>';
                        $buttons .= '</form>'; 
                    }
                    $buttons .= '<form action="' . route('products.destroy', $product->id) . '" method="POST" style="float:left" class="mr-2">';
                    $buttons .= csrf_field();
                    $buttons .= method_field('DELETE');
                    $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1 mr-2" title="Supprimer" onclick="return confirm(\'Êtes-vous sûr?\')"><i class="fas fa-trash"></i></button>';
                    $buttons .= '</form>';
                    return $buttons;
                })
                ->rawColumns(['action', 'description','stock_quantity'])
                ->make(true);
        }
        return abort(404);
    }

/*
    public function getProducts(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['provider', 'categorie'])->select('products.*');
            return DataTables::of($products)
                ->addColumn('reference', function ($product) {
                    return $product->reference ;
                })
                ->addColumn('provider_name', function ($product) {
                    return  $product->provider ? $product->provider->company  : 'N/A';
                })				
                ->addColumn('category_name', function ($product) {
                    return $product->categorie ? $product->categorie->name : 'N/A';
                })	
                ->filterColumn('provider_company', function($query, $keyword) {
                    $query->whereHas('provider', function($q) use ($keyword) {
                        $q->where('company', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('categorie_name', function($query, $keyword) {
                    $query->whereHas('categorie', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })	
                ->addColumn('prix_ttc', function ($product) {
                    return $product->prix_ttc ;
                })		
				->addColumn('name', function ($product) {
                    return $product->name ;
                })				
                ->addColumn('stock_quantity', function ($product) {
                    return $product->stock_quantity ;
                })	
				->editColumn('description', function ($product) {
                    return nl2br($product->description);
                })
                ->filterColumn('provider_company', function($query, $keyword) {
                    $query->whereHas('provider', function($q) use ($keyword) {
                        $q->where('company', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('categorie_name', function($query, $keyword) {
                    $query->whereHas('categorie', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($product) {
					$buttons='';						

                        $buttons .= '<a class="btn btn-sm btn-primary mr-2 mb-1" href="' . route('products.edit', $product->id) . '"><i class="fas fa-edit"></i></a>';
						
						$buttons .= '<form action="'. route('products.duplicate', $product->id) .'" method="POST" style="display:inline;" class="mr-2 ml-2">';
                        $buttons .= csrf_field();
                        $buttons .= '<button title="Dupliquer" type="submit" class="btn btn-secondary mb-1 btn-sm"><i class="fas fa-copy"></i></button>';
                        $buttons .= '</form>'; 
						
						$buttons .= '<form action="' . route('products.destroy', $product->id) . '" method="POST" style="float:left" class="mr-2">';
                        $buttons .= csrf_field();
                        $buttons .= method_field('DELETE');
                        $buttons .= '<button type="submit" class="btn btn-sm btn-danger mb-1 mr-2" title="Supprimer" onclick="return ConfirmDelete();"><i class="fas fa-trash"></i></button>';
                        $buttons .= '</form>';
					return $buttons;						
                })
                ->rawColumns(['action','description'])
                ->make(true);
        }
    }

    */
    /**
     * Afficher le formulaire de création de produit
     */
    public function create()
    {
        $categories = Categorie::all();
        $providers = Provider::all();
        
        // Récupérer les attributs existants pour les couleurs et dimensions
        $colorAttribute = Attribute::where('name', 'Couleur')->first();
        $dimensionAttribute = Attribute::where('name', 'Dimension')->first();
        
        $colors = $colorAttribute ? $colorAttribute->values : collect([]);
        $dimensions = $dimensionAttribute ? $dimensionAttribute->values : collect([]);
        
        return view('products.create', compact('categories', 'providers', 'colors', 'dimensions'));
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Valider les données de base du produit
            $request->validate([
                'name' => 'required',
                'type' => 'required|in:0,1',
                'min_qty' => 'required|integer|min:1',
                'reference' => 'required',
                'prix_achat' => 'required|numeric|min:0',
                'prix_ht' => 'required|numeric|min:0',
                'prix_ttc' => 'required|numeric|min:0',
                'tva' => 'required|numeric|min:0',
            ]);
             $productData = [
                'name' => $request->name,
                'type' => $request->type,
                'reference' => $request->reference,
                'prix_achat' => $request->prix_achat,
                'prix_ht' => $request->prix_ht,
                'prix_ttc' => $request->prix_ttc,
                'tva' => $request->tva ?? 19,
                'categorie_id' => $request->categorie_id,
                'provider_id' => $request->provider_id,
                'description' => $request->description,
                'stock_quantity' => $request->stock_quantity ?? 0,
            ];
            $product = Product::create($productData);
 
             // Si c'est un produit variable
            if ($request->type == 1) {

                // Traitement des variations
                if (isset($request->variations) && is_array($request->variations)) {
                    foreach ($request->variations as $variationData) {
                        // Créer la variation
                        $variation = Variation::create([
                            'product_id' => $product->id,
                            'reference' => $variationData['reference'],
                            'prix_achat' => $variationData['prix_achat'],
                            'prix_ht' => $variationData['prix_ht'],
                            'prix_ttc' => $variationData['prix_ttc'],
                            'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                        ]);

                        // Associer les valeurs d'attributs
                        $attributeValueIds = [];
                        
                        // Traiter la couleur
                        if (!empty($variationData['color_id'])) {
                            $colorAttributeId = $this->getOrCreateAttributeId('Couleur');
                            $colorValueId = $this->getOrCreateAttributeValue($colorAttributeId, $variationData['color_id']);
                            $attributeValueIds[] = $colorValueId;
                        }
                        
                        // Traiter la dimension
                        if (!empty($variationData['dimension_id'])) {
                            $dimensionAttributeId = $this->getOrCreateAttributeId('Dimension');
                            $dimensionValueId = $this->getOrCreateAttributeValue($dimensionAttributeId, $variationData['dimension_id']);
                            $attributeValueIds[] = $dimensionValueId;
                        }
                        
                        // Attacher les valeurs d'attributs à la variation
                        $variation->attributeValues()->attach($attributeValueIds);
                    }
                }
            }

            // Traitement des images
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $index => $file) {
                    $path = $file->store('produits', 'public');
                    
                    $name = $file->getClientOriginalName().'_'.date('d_m_Y_H_i').'.'.$file->getClientOriginalExtension();
                    $path = public_path() . "/produits";
                    $file->move($path, $name);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $name,
                        'is_main' => $index === 0 // Première image comme image principale
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produit créé avec succès');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création du produit: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les détails d'un produit
     */
    public function show(Product $product)
    {
        $product->load(['categorie', 'provider', 'variations.attributeValues.attribute', 'images']);
        return view('products.show', compact('product'));
    }
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Categorie::all();
        $providers = Provider::all();
        
        // Récupérer les attributs existants pour les couleurs et dimensions
        $colorAttribute = Attribute::where('name', 'Couleur')->first();
        $dimensionAttribute = Attribute::where('name', 'Dimension')->first();
        
        $colors = $colorAttribute ? $colorAttribute->values : collect([]);
        $dimensions = $dimensionAttribute ? $dimensionAttribute->values : collect([]);
        
        // Récupérer les informations sur les variations du produit
        $variations = $product->variations;
        
        return view('products.edit', compact('product', 'categories', 'providers', 'colors', 'dimensions', 'variations'));
    }
    
    /**
     * Mettre à jour un produit existant
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($id);
            
            // Valider les données de base du produit
            $request->validate([
                'name' => 'required',
                'type' => 'required|in:0,1',
                'min_qty' => 'required|integer|min:1',
                'reference' => 'required',
                'prix_achat' => 'required|numeric|min:0',
                'prix_ht' => 'required|numeric|min:0',
                'prix_ttc' => 'required|numeric|min:0',
                'tva' => 'required|numeric|min:0',
            ]);
            
            // Mise à jour des données du produit
            $product->update([
                'name' => $request->name,
                'type' => $request->type,
                'reference' => $request->reference,
                'prix_achat' => $request->prix_achat,
                'prix_ht' => $request->prix_ht,
                'prix_ttc' => $request->prix_ttc,
                'tva' => $request->tva ?? 19,
                'categorie_id' => $request->categorie_id,
                'provider_id' => $request->provider_id,
                'description' => $request->description,
                'min_qty' => $request->min_qty,
                'stock_quantity' => $request->stock_quantity ?? 0,
            ]);
            
            // Si c'est un produit variable
            if ($request->type == 1) {
                // Supprimer d'abord les anciennes variations si nécessaire
                if ($request->has('replace_variations')) {
                    // Supprimer les relations avec les valeurs d'attributs
                    foreach ($product->variations as $variation) {
                        $variation->attributeValues()->detach();
                    }
                    // Supprimer les variations
                    $product->variations()->delete();
                }
                
                // Traitement des variations
                if (isset($request->variations) && is_array($request->variations)) {
                    foreach ($request->variations as $variationData) {
                        // Vérifier si la variation existe déjà (mise à jour) ou s'il faut en créer une nouvelle
                        if (!empty($variationData['id'])) {
                            $variation = Variation::find($variationData['id']);
                            if ($variation) {
                                $variation->update([
                                    'reference' => $variationData['reference'],
                                    'prix_achat' => $variationData['prix_achat'],
                                    'prix_ht' => $variationData['prix_ht'],
                                    'prix_ttc' => $variationData['prix_ttc'],
                                    'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                                ]);
                                
                                // Mettre à jour les attributs
                                $variation->attributeValues()->detach();
                            }
                        } else {
                            // Créer une nouvelle variation
                            $variation = Variation::create([
                                'product_id' => $product->id,
                                'reference' => $variationData['reference'],
                                'prix_achat' => $variationData['prix_achat'],
                                'prix_ht' => $variationData['prix_ht'],
                                'prix_ttc' => $variationData['prix_ttc'],
                                'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                            ]);
                        }
                        
                        // Associer les valeurs d'attributs
                        $attributeValueIds = [];
                        
                        // Traiter la couleur
                        if (!empty($variationData['color_id'])) {
                            $colorAttributeId = $this->getOrCreateAttributeId('Couleur');
                            $colorValueId = $this->getOrCreateAttributeValue($colorAttributeId, $variationData['color_id']);
                            $attributeValueIds[] = $colorValueId;
                        }
                        
                        // Traiter la dimension
                        if (!empty($variationData['dimension_id'])) {
                            $dimensionAttributeId = $this->getOrCreateAttributeId('Dimension');
                            $dimensionValueId = $this->getOrCreateAttributeValue($dimensionAttributeId, $variationData['dimension_id']);
                            $attributeValueIds[] = $dimensionValueId;
                        }
                        
                        // Attacher les valeurs d'attributs à la variation
                        $variation->attributeValues()->attach($attributeValueIds);
                    }
                }
            }
            
            // Traitement des images
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $index => $file) {
                    $path = $file->store('products', 'public');
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'is_main' => $index === 0 && $product->images()->count() === 0 // Première image comme principale seulement s'il n'y en a pas déjà
                    ]);
                }
            }
            
            // Gérer les images à supprimer
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                foreach ($request->delete_images as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id == $product->id) {
                        // Supprimer le fichier physique
                        if (Storage::disk('public')->exists($image->path)) {
                            Storage::disk('public')->delete($image->path);
                        }
                        $image->delete();
                    }
                }
            }
            
            // Gérer l'image principale
            if ($request->has('main_image')) {
                $product->images()->update(['is_main' => false]);
                $mainImage = ProductImage::find($request->main_image);
                if ($mainImage && $mainImage->product_id == $product->id) {
                    $mainImage->update(['is_main' => true]);
                }
            }
            
            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produit mis à jour avec succès');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour du produit: ' . $e->getMessage()]);
        }
    }
    /**
     * Supprimer un produit
     */
    public function destroy(Product $product)
    {
        try {
            // Supprimer les images du stockage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
            
            // La suppression en cascade supprimera les variations et autres relations
            $product->delete();
            
            return redirect()->route('products.index')
                ->with('success', 'Produit supprimé avec succès');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression du produit: ' . $e->getMessage()]);
        }
    }

    /**
     * Récupérer l'ID d'un attribut existant ou en créer un nouveau
     */
    private function getOrCreateAttributeId($attributeName)
    {
        $attribute = Attribute::firstOrCreate(['name' => $attributeName]);
        return $attribute->id;
    }

    /**
     * Récupérer l'ID d'une valeur d'attribut existante ou en créer une nouvelle
     */
    private function getOrCreateAttributeValue($attributeId, $valueId)
    {
        // Vérifier si $valueId existe déjà comme ID dans la table
        $existingValue = AttributeValue::find($valueId);
        
        if ($existingValue) {
            // Si la valeur existe, on vérifie qu'elle est bien associée à l'attribut
            if ($existingValue->attribute_id == $attributeId) {
                return $existingValue->id;
            }
        }
        
        // Si $valueId est une chaîne de caractères (nouvelle valeur)
        if (!is_numeric($valueId)) {
            // Vérifier si la valeur existe déjà pour cet attribut
            $existingValue = AttributeValue::where('attribute_id', $attributeId)
                                        ->where('value', $valueId)
                                        ->first();
            
            if ($existingValue) {
                return $existingValue->id;
            }
            
            // Créer une nouvelle valeur
            $newValue = AttributeValue::create([
                'attribute_id' => $attributeId,
                'value' => $valueId
            ]);
            
            return $newValue->id;
        }
        
        // Si on arrive ici, c'est que $valueId est un ID numérique mais n'existe pas
        // ou appartient à un autre attribut
        return null;
    }

    /**
     * Définir une image comme image principale
     */
    public function setMainImage(Request $request, $id)
    {
        $productImage = ProductImage::findOrFail($id);
        $product = $productImage->product;
        
        // Réinitialiser toutes les images du produit
        $product->images()->update(['is_main' => false]);
        
        // Définir l'image sélectionnée comme principale
        $productImage->update(['is_main' => true]);
        
        return back()->with('success', 'Image principale définie avec succès');
    }

    /**
     * Supprimer une image
     */
    public function deleteImage($id)
    {
        $productImage = ProductImage::findOrFail($id);
        
        // Vérifier si c'est l'image principale et s'il existe d'autres images
        $isMain = $productImage->is_main;
        $product = $productImage->product;
        
        // Supprimer l'image du stockage
        Storage::disk('public')->delete($productImage->path);
        
        // Supprimer l'enregistrement de la base de données
        $productImage->delete();
        
        // Si c'était l'image principale et qu'il reste d'autres images, définir la première comme principale
        if ($isMain && $product->images->count() > 0) {
            $product->images->first()->update(['is_main' => true]);
        }
        
        return back()->with('success', 'Image supprimée avec succès');
    }



        
    /**
     * Dupliquer un produit simple
     *
     * @param int $id ID du produit à dupliquer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicateProduct($id)
    {
        DB::beginTransaction();
        
        try {
            // Récupérer le produit source
            $sourceProduct = Product::with(['images'])->findOrFail($id);
            
            // Vérifier que c'est un produit simple
            if ($sourceProduct->type != 0) {
                return back()->withErrors(['error' => 'Seuls les produits simples peuvent être dupliqués avec cette fonction.']);
            }
            
            // Créer un nouveau produit avec les mêmes attributs
            $newProduct = $sourceProduct->replicate();
            $newProduct->name = 'Copie de ' . $newProduct->name;
            
            // Générer une nouvelle référence unique
            $newProduct->reference = $sourceProduct->reference . '-copy-' . time();
            
            // Réinitialiser le stock si nécessaire
            $newProduct->stock_quantity = 0;
            
            $newProduct->save();
            
            // Dupliquer les images
            foreach ($sourceProduct->images as $image) {
                // Copier le fichier physiquement dans le stockage
                $originalPath = $image->path;
                $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                $newFileName = 'products/' . uniqid() . '.' . $extension;
                
                if (Storage::disk('public')->exists($originalPath)) {
                    Storage::disk('public')->copy($originalPath, $newFileName);
                    
                    // Créer l'enregistrement d'image
                    ProductImage::create([
                        'product_id' => $newProduct->id,
                        'path' => $newFileName,
                        'is_main' => $image->is_main
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produit dupliqué avec succès');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la duplication du produit: ' . $e->getMessage()]);
        }
    }



    
 
}