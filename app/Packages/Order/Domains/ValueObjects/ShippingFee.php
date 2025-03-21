<?php

namespace App\Packages\Order\Domains\ValueObjects;

use App\Packages\Shared\Domains\ValueObjects\Price;

class ShippingFee extends Price
{
    /**
     * @param int $priceWithoutTax 税抜き送料
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
     * 送料無料かどうかを確認
     */
    public function isFree(): bool
    {
        return $this->getPriceWithTax() === 0;
    }

    /**
     * 送料無料のインスタンスを作成
     */
    public static function free(): self
    {
        return new self(0);
    }
}
