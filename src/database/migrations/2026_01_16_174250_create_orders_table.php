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
            $table->id();
            // 注文があるユーザーは削除できないように（履歴を守る）
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // 注文がある商品は削除できないように（履歴を守る）
            $table->foreignId('product_id')->constrained()->restrictOnDelete()->unique();
            $table->integer('price');
            $table->string('shipping_postcode', 8);
            $table->string('shipping_address', 255);
            $table->string('shipping_building', 255)->nullable();
            $table->string('shipping_name', 255);
            // 1=コンビニ決済, 2=カード決済
            $table->tinyInteger('payment_method');
            // 1=pending, 2=paid, 3=canceled, 4=expired
            $table->tinyInteger('status')->default(1);
            $table->string('stripe_checkout_session_id', 255)->nullable();
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
