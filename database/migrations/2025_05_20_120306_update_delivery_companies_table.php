<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeliveryCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('delivery_companies', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('api_url_dev');
            $table->string('api_url_prod');
            $table->string('code_api');
            $table->string('cle_api');
            $table->boolean('is_active')->default(true);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('delivery_companies', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('api_url_dev');
            $table->dropColumn('api_url_prod');
            $table->dropColumn('code_api');
            $table->dropColumn('cle_api');
            $table->dropColumn('is_active');
        });
    }
}
