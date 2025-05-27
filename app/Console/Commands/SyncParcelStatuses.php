<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeliveryCompany;
use App\Services\DeliveryService;

class SyncParcelStatuses extends Command
{
    protected $signature = 'parcels:sync-statuses';
    protected $description = 'Synchroniser les statuts des colis depuis les APIs des sociétés de livraison';

    public function handle()
    {
        $this->info('⏳ Démarrage de la synchronisation des colis...');

        $companies = DeliveryCompany::where('is_active', true)->get();

        foreach ($companies as $company) {
            $this->info('📦 Traitement : ' . $company->name);
            try {
                $service = new DeliveryService($company);
                $service->syncParcelStatuses();
                $this->info('✅ Colis synchronisés pour : ' . $company->name);
            } catch (\Exception $e) {
                $this->error('❌ Erreur pour ' . $company->name . ': ' . $e->getMessage());
            }
        }

        $this->info('🎉 Synchronisation terminée.');
    }
}


//* * * * * cd /chemin/vers/ton-projet && php artisan schedule:run >> /dev/null 2>&1
//php artisan parcels:sync-statuses