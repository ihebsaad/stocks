<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCompany;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    protected function getService($companyName)
    {
        $company = DeliveryCompany::where('name', $companyName)->where('is_active', true)->firstOrFail();
        return new DeliveryService($company);
    }

    public function list($company)
    {
        $service = $this->getService($company);
        return response()->json($service->listParcels());
    }

    public function get($company, $barcode)
    {
        $service = $this->getService($company);
        return response()->json($service->getParcel($barcode));
    }

    public function create(Request $request, $company)
    {
        $service = $this->getService($company);
        return response()->json($service->createParcel($request->all()));
    }
}
