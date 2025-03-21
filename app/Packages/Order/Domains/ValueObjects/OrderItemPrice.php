<?php

namespace App\Packages\Order\Domains\ValueObjects;

use App\Packages\Shared\Domains\ValueObjects\Price;

class OrderItemPrice extends Price
{
    /**
     * @param int $priceWithoutTax 税抜き販売価格
     * @param float $taxRate 税率（デフォルト10%）
     * @param int $roundingMode 端数処理方法
     */
    public function __construct(
        int $priceWithoutTax,
        float $taxRate = self::CONSUMPTION_TAX_RATE,
        int $roundingMode = self::ROUNDING_MODE_ROUND
    ) {
        parent::__construct($priceWithoutTax, $taxRate, $roundingMode);
    }

    /**
     * 無料商品かどうかを確認
     */
    public function isFree(): bool
    {
        return $this->getPriceWithTax() === 0;
    }

    /**
     * 軽減税率を適用した新しいインスタンスを作成
     */
    public static function withReducedTaxRate(
        int $priceWithoutTax,
        int $roundingMode = self::ROUNDING_MODE_ROUND
    ): self {
        return new self($priceWithoutTax, self::REDUCED_TAX_RATE, $roundingMode);
    }

    /**
     * 非課税の新しいインスタンスを作成
     */
    public static function taxExempt(
        int $priceWithoutTax,
        int $roundingMode = self::ROUNDING_MODE_ROUND
    ): self {
        return new self($priceWithoutTax, 0.00, $roundingMode);
    }
}
