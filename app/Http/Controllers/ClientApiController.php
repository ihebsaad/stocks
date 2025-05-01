<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientApiController extends Controller
{
    /**
     * Recherche de client par téléphone pour Select2
     */
    public function search(Request $request)
    {
        $term = $request->input('q');
        $clients = Client::where('phone', 'like', "%{$term}%")
            ->orWhere('phone2', 'like', "%{$term}%")
            ->orWhere('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->get(['id', 'phone', 'first_name', 'last_name']);
            
        $formatted = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'text' => "{$client->phone} - {$client->first_name} {$client->last_name}",
                // Inclure les détails du client pour autocomplétion
                'client_details' => [
                    'phone' => $client->phone,
                    'phone2' => $client->phone2,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'city' => $client->city,
                    'delegation' => $client->delegation,
                    'address' => $client->address,
                    'postal_code' => $client->postal_code,
                ]
            ];
        });
        
        return response()->json(['results' => $formatted]);
    }
    
    /**
     * Récupérer les détails d'un client par ID
     */
    public function getClientDetails($id)
    {
        $client = Client::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'client' => $client
        ]);
    }
    
    /**
     * Vérifier si un numéro de téléphone existe déjà
     */
    public function checkPhone(Request $request)
    {
        $phone = $request->input('phone');
        $client = Client::where('phone', $phone)->first();
        
        if ($client) {
            return response()->json([
                'exists' => true,
                'client' => $client
            ]);
        }
        
        return response()->json(['exists' => false]);
    }
}