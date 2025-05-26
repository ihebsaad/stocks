<?php

namespace App\Services;

use App\Models\DeliveryCompany;
use App\Models\Parcel;
use App\Models\OrderStatusHistory;
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
            'id'=> $this->company->code_api,
            'cle_api' => $this->company->cle_api,
        ]);
    }

    public function getParcel($codeBarre)
    {
        return $this->postRequest([
            'action' => 'get',
            'code_api' => $this->company->code_api,
            'id'=> $this->company->code_api,
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
        
        $data = $this->mapParcelFields($data);
        dd($data);
        return $this->postRequest($data);
    }

 
    public function updateParcel(array $data)
    {
        $data = array_merge([
            'action' => 'update',
            'code_api' => $this->company->code_api,
            'cle_api' => $this->company->cle_api,
        ], $data);

        $data = $this->mapParcelFields($data);

        return $this->postRequest($data);
    }

    public function deleteParcel($codeBarre)
    {
        return $this->postRequest([
            'action' => 'delete',
            'code_api' => $this->company->code_api,
            'id'=> $this->company->code_api,
            'cle_api' => $this->company->cle_api,
            'code_barre' => $codeBarre,
        ]);
    }

    protected function postRequest(array $params)
    {
        return Http::asForm()->post($this->baseUrl, $params)->json();
    }


    public function syncParcelStatuses()
    {
        $response = $this->postRequest([
            'action' => 'list',
            'code_api' => $this->company->code_api,
            'cle_api' => $this->company->cle_api,
            'id'=> $this->company->code_api,
        ]);

        foreach ($response as $item) {
            $code = $item['code_barre'] ?? null;
            $etat = $item['dernier_etat'] ?? null;
            $dateEtat = $item['date_dernier_etat'] ?? ($item['date_d_e'] ?? null);

            if (!$code || !$etat) continue;

            $parcel = Parcel::where('reference', $code)->first();
            if (!$parcel) continue;

            if ($parcel->dernier_etat !== $etat || $parcel->date_dernier_etat !== $dateEtat) {
                $old = $parcel->dernier_etat;
                $parcel->update([
                    'dernier_etat' => $etat,
                    'date_dernier_etat' => $dateEtat,
                ]);

                OrderStatusHistory::create([
                    'order_id'   => $parcel->order_id,
                    'user_id'    =>  null,//api
                    'old_status' => $old,
                    'new_status' => $etat,
                    'comment'    => 'Mise à jour automatique via API ' . $this->company->name,
                ]);
            }
        }
    }


    
    protected function getFieldMap(): array
    {
        $company = strtolower($this->company->name);

        return match ($company) {
            'coliexpress' => [
                'code_api'     => 'id',
                'tel_l'        => 'tel_cl',
                'tel2_l'       => 'tel_2_cl',
                'nom_client'   => 'nom_prenom_cl',
                'gov_l'        => 'ville_cl',
                'adresse_l'    => 'adresse_cl',
                'delegation'   => 'delegation_cl',
            ],
            default => [ // Droppex ou autres
                // Pas de changement
            ],
        };
    }

    protected function mapParcelFields(array $parcelData): array
    {
        $fieldMap = $this->getFieldMap();

        foreach ($fieldMap as $old => $new) {
            if (array_key_exists($old, $parcelData)) {
                $parcelData[$new] = $parcelData[$old];
                unset($parcelData[$old]);
            }
        }

        return $parcelData;
    }

}
