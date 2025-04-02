<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * タイムスタンプの設定
     */
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    /**
     * 代入可能な属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'order_id',
        'name',
        'price_with_tax',
        'price_without_tax',
        'price_tax_rate',
        'quantity',
        'created_at',
        'updated_at',
    ];

    /**
     * 属性のキャスト
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_with_tax' => 'integer',
        'price_without_tax' => 'integer',
        'price_tax_rate' => 'float',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 注文へのリレーション
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
