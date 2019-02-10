<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('source');
            $table->string('image')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('active_to')->useCurrent();
            $table->string('coupon_type')->nullable();
            $table->string('promo_code')->nullable();
            $table->string('offer_name');
            $table->integer('offer_id');
            $table->string('status')->nullable();
            $table->string('status_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('action_category_name')->nullable();
            $table->integer('coupon_category_id')->nullable();
            $table->string('coupon_category_name')->nullable();
            $table->string('url')->nullable();
            $table->string('url_frame')->nullable();
            $table->string('look')->nullable();
            $table->string('domain')->nullable();
            $table->timestamps();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index(['id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
