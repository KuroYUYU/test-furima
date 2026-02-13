<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'price',
        'shipping_postcode',
        'shipping_address',
        'shipping_building',
        'shipping_name',
        'payment_method',
        'status',
        'stripe_checkout_session_id',
    ];

    // 決済の定数を記述（今回キャンセルは使わないが定義としてのみ記述）
    public const STATUS_PENDING  = 1;
    public const STATUS_PAID     = 2;
    // public const STATUS_CANCELED = 3;

    public const STATUS_LABELS = [
        self::STATUS_PENDING  => '支払い待ち',
        self::STATUS_PAID     => '支払い完了',
        // self::STATUS_CANCELED => 'キャンセル',
    ];

    // 購入者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 購入商品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
