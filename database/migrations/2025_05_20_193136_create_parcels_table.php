<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_company_id')->constrained();
            $table->string('tel_l');
            $table->string('tel2_l')->nullable();
            $table->string('nom_client');
            $table->string('gov_l');
            $table->string('adresse_l');
            $table->string('cod');
            $table->string('libelle')->nullable();
            $table->string('nb_piece')->nullable();
            $table->text('remarque')->nullable();
            $table->string('service')->default('Livraison');
            $table->string('status')->default('en attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcels');
    }
}
