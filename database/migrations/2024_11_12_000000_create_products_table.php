<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migration pour la table products (mise à jour)
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->tinyInteger('type')->default(0); // 0 simple, 1 variable
            $table->string('reference')->nullable();
            $table->double('prix_achat', 8, 2)->nullable();
            $table->double('prix_ht', 8, 2)->nullable();
            $table->double('prix_ttc', 8, 2)->nullable();
            $table->double('tva', 8, 2)->default(19);
            $table->integer('min_qty')->default(1);
            $table->integer('stock_quantity')->default(10);
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('categorie_id')->nullable();
            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('set null');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('set null');
            $table->timestamps();
        });

        // Migration pour la table attributes
        Schema::create('attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        // Migration pour la table attribute_values
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attribute_id');
            $table->string('value');
            $table->timestamps();
            
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        });

        // Migration pour la table variations
        Schema::create('variations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('reference')->nullable();
            $table->double('prix_achat', 8, 2);
            $table->double('prix_ht', 8, 2);
            $table->double('prix_ttc', 8, 2);
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Migration pour la table variation_attribute_values
        Schema::create('variation_attribute_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('variation_id');
            $table->unsignedBigInteger('attribute_value_id');
            
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
            
            // Pour éviter les duplications
            $table->unique(['variation_id', 'attribute_value_id'], 'var_attr_val_unique');

        });

        // Migration pour la table product_images
        Schema::create('product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('path');
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('variation_attribute_values');
        Schema::dropIfExists('variations');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('products');
    }
}
