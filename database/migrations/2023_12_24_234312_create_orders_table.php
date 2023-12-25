<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('order_id', 64)->unique()->primary();
            $table->timestamp('order_date', 6);
            $table->integer('total')->unsigned()->default(0)->nullable();
            $table->tinyInteger('status')->unsigned()->default(0)->nullable();
            $table->string('user_id', 64);
            $table->timestamps();
            $table->index(['order_id', 'user_id', 'order_date', 'created_at']);
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
