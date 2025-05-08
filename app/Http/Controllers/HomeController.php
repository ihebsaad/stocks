<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reglement;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
         
        $commandes=Order::count();
        $produits=Product::count();

        return view('home',compact('commandes','produits'));
 
    }


    public function getInvoicesByProduct($productId)
    {
        // Récupérer les factures contenant le produit spécifié
        $invoices = Invoice::whereHas('items', function ($query) use ($productId) {
            $query->where('product', $productId);
        })->with(['customer', 'items'])->get();

        // Formater les données pour la réponse JSON
        $data = $invoices->map(function ($invoice) {
            $customer=Customer::find($invoice->customer);
            return [
                'invoice_number' => $invoice->id,
                'date' => $invoice->created_at->format('d/m/Y'),
                'customer' => $customer->name.' '.$customer->lastname,
                'total_ttc' => $invoice->total_ttc,
            ];
        });

        return response()->json($data);
    }
}
