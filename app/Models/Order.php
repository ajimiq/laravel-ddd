<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * プライマリーキーの設定
     */
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * タイムスタンプの設定
     */
    public const CREATED_AT = 'ordered_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * 代入可能な属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'ec_site_code',
        'status',
        'ordered_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'shipping_fee_with_tax',
        'shipping_fee_without_tax',
        'shipping_fee_tax_rate',
        'total_amount_with_tax',
        'total_amount_without_tax',
        'created_at',
        'updated_at',
        'canceled_at',
        'cancel_reason',
    ];

    /**
     * 属性のキャスト
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ordered_at' => 'datetime',
        'canceled_at' => 'datetime',
        'shipping_fee_with_tax' => 'integer',
        'shipping_fee_without_tax' => 'integer',
        'shipping_fee_tax_rate' => 'float',
        'total_amount_with_tax' => 'integer',
        'total_amount_without_tax' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 注文商品のリレーション
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * ECサイトを取得
     */
    public function ecSite()
    {
        return $this->belongsTo(EcSite::class, 'ec_site_code', 'code');
    }
}
