<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            //コメントしたユーザーが存在しないならコメントも消える
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            //商品が削除などされた場合コメントも消える
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('body', 255);
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
        Schema::dropIfExists('comments');
    }
}
