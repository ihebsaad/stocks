<?php

namespace App\Services;

use App\Models\DeliveryCompany;
use Illuminate\Support\Facades\Http;

class DeliveryService
{
    protected $company;
    protected $baseUrl;

    public function __construct(DeliveryCompany $company)
    {
        $this->company = $company;
        $this->baseUrl = app()->environment('production') ? $company->api_url_prod : $company->api_url_dev;
    }

    public function listParcels()
    {
        return $this->postRequest([
            'action' => 'list',
            'code_api' => $this->company->code_api,
            'cle_api' => $this->company->cle_api,
        ]);
    }

    public function getParcel($codeBarre)
    {
        return $this->postRequest([
            'action' => 'get',
            'code_api' => $this->company->code_api,
            'cle_api' => $this->company->cle_api,
            'code_barre' => $codeBarre,
        ]);
    }

    public function createParcel(array $data)
    {
        $data = array_merge([
            'action' => 'add',
            'code_api' => $this->company->code_api,
            'cle_api' => $this->company->cle_api,
        ], $data);

        return $this->postRequest($data);
    }
 

    public function deleteParcel($codeBarre)
    {
        return $this->postRequest([
            'action' => 'delete',
            'code_api' => $this->company->code_api,
            'cle_api' => $this->company->cle_api,
            'code_barre' => $codeBarre,
        ]);
    }

    protected function postRequest(array $params)
    {
        return Http::asForm()->post($this->baseUrl, $params)->json();
    }
}
