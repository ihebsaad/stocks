<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'code' => 'required|string|unique:promo_codes,code',
                'type' => 'required|in:percentage,fixed_amount,free_product',
                'value' => 'required_unless:type,free_product|nullable|numeric|min:0',
                'product_id' => 'required_if:type,free_product|nullable|exists:products,id',
                'expires_at' => 'nullable|date|after:today',
                'apply_immediately' => 'boolean'
            ]);

            // Validation spécifique pour le pourcentage
            if ($validated['type'] === 'percentage' && isset($validated['value']) && $validated['value'] > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le pourcentage ne peut pas dépasser 100%'
                ], 422);
            }

            $promoCode = PromoCode::create([
                'client_id' => $validated['client_id'],
                'code' => strtoupper($validated['code']),
                'type' => $validated['type'],
                'value' => $validated['value'] ?? null,
                'product_id' => $validated['product_id'] ?? null,
                'expires_at' => $validated['expires_at'] ? \Carbon\Carbon::parse($validated['expires_at']) : null,
                'is_used' => false,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Code promo créé avec succès',
                'promo_code' => $promoCode,
                'apply_immediately' => $request->boolean('apply_immediately')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur création code promo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du code promo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PromoCode $promoCode)
    {
        try {
            $promoCode->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Code promo supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression code promo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du code promo'
            ], 500);
        }
    }

    public function use(PromoCode $promoCode, Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'discount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            // AJOUTER: Vérifier si le code promo est valide
            if (!$promoCode->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code promo non valide ou expiré'
                ], 422);
            }

            // AJOUTER: Vérifier si le code promo n'est pas déjà utilisé
            if ($promoCode->is_used) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code promo déjà utilisé'
                ], 422);
            }

            $order = Order::findOrFail($validated['order_id']);

            // CORRIGER: Utiliser une transaction pour éviter les problèmes de concurrence
            \DB::transaction(function() use ($order, $promoCode, $validated) {
                // Mettre à jour la commande
                $order->update([
                    'discount' => $validated['discount'],
                    'total' => $validated['total'],
                    'promo_code_id' => $promoCode->id
                ]);

                // Marquer le code promo comme utilisé
                $promoCode->markAsUsed();
            });

            return response()->json([
                'success' => true,
                'message' => 'Code promo utilisé avec succès',
                'order' => $order->fresh() // Retourner les données fraîches
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur utilisation code promo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'utilisation du code promo: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJOUTER: Méthode pour désactiver un code promo
    public function remove(PromoCode $promoCode, Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Order::findOrFail($validated['order_id']);

            // CORRIGER: Utiliser une transaction
            \DB::transaction(function() use ($order, $promoCode) {
                // Réinitialiser la commande
                $order->update([
                    'discount' => 0,
                    'promo_code_id' => null
                ]);

                // Marquer le code promo comme non utilisé s'il était utilisé par cette commande
                if ($promoCode->is_used) {
                    $promoCode->update(['is_used' => false]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Code promo retiré avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur retrait code promo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du retrait du code promo'
            ], 500);
        }
    }
}