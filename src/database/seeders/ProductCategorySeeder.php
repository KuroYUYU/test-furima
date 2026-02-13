<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 商品とカテゴリの対応表
        $map = [
            '腕時計' => ['ファッション', 'メンズ', 'アクセサリー'],
            'HDD' => ['家電'],
            '玉ねぎ3束' => ['キッチン'],
            '革靴' => ['ファッション', 'メンズ'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['ファッション', 'レディース'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン'],
            'メイクセット' => ['コスメ', 'レディース'],
        ];

        $categoryIds = Category::pluck('id', 'name');

        foreach ($map as $productName => $categoryNames) {
            $product = Product::where('name', $productName)->first();
            if (!$product) continue;

            $attachIds = [];
            foreach ($categoryNames as $cName) {
                if (isset($categoryIds[$cName])) {
                    $attachIds[] = $categoryIds[$cName];
                }
            }

            // 重複防止
            $product->categories()->sync($attachIds);
        }
    }
}
