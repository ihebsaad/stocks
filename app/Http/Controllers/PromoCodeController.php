<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'code' => 'required|string|unique:promo_codes,code',
            'type' => 'required|in:percentage,fixed_amount,free_product',
            'value' => 'required_unless:type,free_product|numeric|min:0',
            'product_id' => 'required_if:type,free_product|exists:products,id',
            'expires_at' => 'nullable|date|after:today',
            'apply_immediately' => 'boolean'
        ]);

        // Validation spécifique pour le pourcentage
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Le pourcentage ne peut pas dépasser 100%'
            ], 422);
        }

        try {
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
                'promo_code' => $promoCode,
                'apply_immediately' => $request->boolean('apply_immediately')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du code promo'
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
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du code promo'
            ], 500);
        }
    }
}