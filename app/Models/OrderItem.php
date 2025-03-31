<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    // /**
    //  * プライマリーキーの設定
    //  */
    // protected $primaryKey = 'item_id';
    // public $incrementing = false;
    // protected $keyType = 'string';

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

    /**
     * 商品の小計を取得
     */
    public function getSubtotal(): int
    {
        return $this->price_with_tax * $this->quantity;
    }

    /**
     * 税率を取得
     */
    public function getTaxRate(): float
    {
        return $this->price_tax_rate;
    }

    /**
     * 税抜価格を取得
     */
    public function getPriceWithoutTax(): int
    {
        return $this->price_without_tax;
    }

    /**
     * 税額を取得
     */
    public function getTaxAmount(): int
    {
        return $this->price_with_tax - $this->price_without_tax;
    }
}
