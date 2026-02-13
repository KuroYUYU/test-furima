<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // 現状ユーザー削除機能は未実装だがユーザーが存在しない場合プロフィールも削除
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('nickname', 20);
            $table->string('profile_image_path', 255)->nullable();
            $table->string('postcode', 8);
            $table->string('address', 255);
            $table->string('building', 255)->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
