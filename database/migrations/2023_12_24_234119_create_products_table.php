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
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 64)->primary();
            $table->string('product_name', 100);
            $table->string('product_type_id', 64);
            $table->integer('price')->unsigned()->default(0)->nullable();
            $table->timestamps();
            $table->index(['product_id', 'product_name', 'product_type_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
