<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupSlipsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_slips', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference')->unique();
            $table->foreignId('delivery_company_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('status', ['pending', 'confirmed', 'picked_up', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Migration pour la table pivot pickup_slip_parcels
        Schema::create('pickup_slip_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_slip_id')->constrained()->onDelete('cascade');
            $table->foreignId('parcel_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['pickup_slip_id', 'parcel_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_slips');
        Schema::dropIfExists('pickup_slip_parcels');
    }
}
