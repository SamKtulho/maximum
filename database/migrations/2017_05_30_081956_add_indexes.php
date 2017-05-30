<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function ($table) {
            $table->index('is_valid');
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->index(['status', 'type']);
        });

        Schema::table('links', function (Blueprint $table) {
            $table->index(['status', 'registrar']);
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
    }
}
