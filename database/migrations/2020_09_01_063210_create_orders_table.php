<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;


class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('orders', function (Blueprint $table) {
            $currentDate = Carbon::now()->toDateTimeString();
            $table->increments('id');
            $table->datetime('date_create')->default($currentDate);
            $table->bigInteger('total_price')->default(0);
            $table->float('cash_given')->default(0);
            $table->float('cash_return')->default(0);
            $table->float('discount')->default(0);
            $table->integer('user_id')->unsigned();
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
