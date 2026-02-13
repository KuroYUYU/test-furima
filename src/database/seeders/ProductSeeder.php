<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーidは必須のため作成させる
        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            ['name' => 'Seeder Seller', 'password' => Hash::make('password')]
        );

        $now = now();

        Product::insert([
            [
                'user_id' => $seller->id,
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'user_id' => $seller->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
