<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migration pour les sociétés de livraison
        Schema::create('delivery_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('delivery_price', 10, 3);
            $table->string('manager_name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Si vous n'avez pas déjà une table clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('phone2')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('city');
            $table->string('delegation');
            $table->string('address');
            $table->string('postal_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        // Migration pour les commandes
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('delivery_company_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('service_type', ['Livraison', 'Echange'])->nullable();
            $table->enum('status', ['draft','pending','no_stock','production','no_response','cancelled','confirmed','not_available'])->default('draft');
            $table->text('notes')->nullable();
            $table->boolean('free_delivery')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Migration pour les images de commandes
        Schema::create('order_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });

        // Migration pour l'historique des statuts
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
