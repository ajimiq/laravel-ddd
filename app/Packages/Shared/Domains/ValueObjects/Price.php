<?php

namespace App\Packages\Shared\Domains\ValueObjects;

use InvalidArgumentException;

class Price
{
    protected const CONSUMPTION_TAX_RATE = 0.10; // 消費税率10%
    protected const REDUCED_TAX_RATE = 0.08;     // 軽減税率8%
    protected const VALID_TAX_RATES = [0.00, 0.08, 0.10]; // 有効な税率一覧

    protected const ROUNDING_MODE_ROUND = PHP_ROUND_HALF_UP;    // 四捨五入（デフォルト）
    protected const ROUNDING_MODE_CEIL = PHP_ROUND_HALF_UP + 1; // 切り上げ
    protected const ROUNDING_MODE_FLOOR = PHP_ROUND_HALF_DOWN - 1; // 切り捨て
    protected const VALID_ROUNDING_MODES = [
        self::ROUNDING_MODE_ROUND,
        self::ROUNDING_MODE_CEIL,
        self::ROUNDING_MODE_FLOOR,
    ];

    /** @var int */
    protected int $priceWithoutTax;

    /** @var float */
    protected float $taxRate;

    /** @var int */
    protected int $roundingMode;

    /**
     * @param int $priceWithoutTax 税抜き価格
     * @param float $taxRate 税率（0.00, 0.08, 0.10のいずれか）
     * @param int $roundingMode 端数処理方法
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $priceWithoutTax,
        float $taxRate = self::CONSUMPTION_TAX_RATE,
        int $roundingMode = self::ROUNDING_MODE_ROUND
    ) {
        if ($priceWithoutTax < 0) {
            throw new InvalidArgumentException('価格は0以上である必要があります。');
        }

        if (!in_array($taxRate, self::VALID_TAX_RATES, true)) {
            throw new InvalidArgumentException('無効な税率です。有効な税率: 0%, 8%, 10%');
        }

        if (!in_array($roundingMode, self::VALID_ROUNDING_MODES, true)) {
            throw new InvalidArgumentException('無効な端数処理方法です。');
        }

        $this->priceWithoutTax = $priceWithoutTax;
        $this->taxRate = $taxRate;
        $this->roundingMode = $roundingMode;
    }

    /**
     * 税抜き価格を取得
     */
    public function getPriceWithoutTax(): int
    {
        return $this->priceWithoutTax;
    }

    /**
     * 消費税額を取得
     */
    public function getTaxAmount(): int
    {
        $taxAmount = $this->priceWithoutTax * $this->taxRate;

        return match ($this->roundingMode) {
            self::ROUNDING_MODE_CEIL => (int) ceil($taxAmount),
            self::ROUNDING_MODE_FLOOR => (int) floor($taxAmount),
            default => (int) round($taxAmount),
        };
    }

    /**
     * 税込み価格を取得
     */
    public function getPriceWithTax(): int
    {
        return $this->priceWithoutTax + $this->getTaxAmount();
    }

    /**
     * 適用される税率を取得
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * 軽減税率適用商品かどうかを確認
     */
    public function isReducedTaxRate(): bool
    {
        return $this->taxRate === self::REDUCED_TAX_RATE;
    }

    /**
     * 価格を加算
     */
    public function add(Price $other): self
    {
        if ($this->taxRate !== $other->getTaxRate()) {
            throw new InvalidArgumentException('異なる税率の価格は加算できません。');
        }

        return new self(
            $this->priceWithoutTax + $other->getPriceWithoutTax(),
            $this->taxRate,
            $this->roundingMode
        );
    }

    /**
     * 価格を比較
     */
    public function equals(Price $other): bool
    {
        return $this->priceWithoutTax === $other->getPriceWithoutTax() &&
            $this->taxRate === $other->getTaxRate();
    }

    /**
     * 文字列に変換
     */
    public function __toString(): string
    {
        return (string) $this->priceWithoutTax;
    }
}
