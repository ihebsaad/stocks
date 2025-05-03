<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Recherche de client par téléphone pour Select2
     */
    public function search(Request $request)
    {
        $term = $request->input('q');
        $type = $request->input('type');
        
        $query = Client::query();
        
        // Si type=phone, limiter la recherche au numéro de téléphone
        if ($type === 'phone') {
            $query->where('phone', 'like', "%{$term}%")
                  ->orWhere('phone2', 'like', "%{$term}%");
        } else {
            // Sinon, recherche normale (téléphone, nom, prénom)
            $query->where('phone', 'like', "%{$term}%")
                  ->orWhere('phone2', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%");
        }
        
        $clients = $query->get();
            
        $formatted = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'text' => "{$client->phone} - {$client->first_name} {$client->last_name}",
                // Inclure tous les détails du client pour autocomplétion
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
     * Amélioré pour retourner tous les champs du client
     */
    public function checkPhone(Request $request)
    {
        $phone = $request->input('phone');
        // Recherche exacte par téléphone
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