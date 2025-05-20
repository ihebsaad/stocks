<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\DeliveryCompany;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class ParcelController extends Controller
{
    public function store(Order $order)
    {
        $client = $order->client;
        $deliveryCompany = $order->deliveryCompany;

        if (!$deliveryCompany) {
            return back()->with('error', 'Aucune société de livraison sélectionnée.');
        }

        // Créer l'enregistrement local Parcel
        $parcel = Parcel::create([
            'order_id' => $order->id,
            'delivery_company_id' => $deliveryCompany->id,
            'tel_l' => $client->phone,
            'tel2_l' => $client->phone2,
            'nom_client' => $client->first_name . ' ' . $client->last_name,
            'gov_l' => $client->city,
            'adresse_l' => $client->address,
            'cod' => $order->total,
            'libelle' => $order->items->first()->product->name ?? 'Commande',
            'nb_piece' => $order->items->count(),
            'remarque' => $order->status_comment,
            'service' => $order->service_type,
        ]);

        // Envoyer à l'API
        $deliveryService = new DeliveryService($deliveryCompany);
        $apiResponse = $deliveryService->createParcel($parcel->toArray());

        // Traitement de la réponse
        if (isset($apiResponse['success']) && $apiResponse['success']) {
            $parcel->status = 'envoyé';
            $parcel->save();
            return back()->with('success', 'Colis créé et envoyé à la société de livraison.');
        } else {
            return back()->with('error', 'Erreur API: ' . json_encode($apiResponse));
        }
    }
}
