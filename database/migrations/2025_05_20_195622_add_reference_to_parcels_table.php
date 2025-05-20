<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceToParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('reference')->nullable();
            $table->string('tracking_url')->nullable();
            $table->text('api_message')->nullable();
            $table->text('dernier_etat')->nullable();
            $table->text('date_dernier_etat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn(['reference', 'tracking_url', 'api_message','dernier_etat','date_dernier_etat']);
        });
    }
}
