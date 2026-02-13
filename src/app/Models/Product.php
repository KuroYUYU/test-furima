<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand_name',
        'description',
        'price',
        'condition',
        'image_path',
    ];

    public function scopeKeywordSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where('name', 'LIKE', '%' . $keyword . '%');
    }

    // 各リレーション
    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ（多対多）
    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'product_category');
    }

    // いいね（1対多）
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // コメント（1対多）
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 注文（sold判定に使う）
    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
