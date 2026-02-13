<?php

use Faker\Guesser\Name;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\stringContains;

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
            $table->id();
            // 今回削除機能はないがユーザーが消えても出品の記録は残しておく（購入後のトラブル防止の想定）
            $table->foreignId('user_id')->constrained();
            $table->string('name', 255);
            $table->string('image_path', 255);
            $table->string('brand_name', 255)->nullable();;
            $table->tinyInteger('condition');
            $table->integer('price');
            $table->string('description', 255);
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
        Schema::dropIfExists('products');
    }
}
