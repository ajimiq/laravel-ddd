<?php

namespace Tests\Unit\Order;

use PHPUnit\Framework\TestCase;
use App\Packages\Order\Domains\ValueObjects\OrderItemPrice;
use App\Packages\Shared\Domains\ValueObjects\Price;

class OrderItemPriceTest extends TestCase
{
    public function test_通常税率の価格計算が正しく行われる(): void
    {
        // 税抜1000円、税率10%の商品
        $price = new OrderItemPrice(1000, 0.1);

        $this->assertEquals(1000, $price->getPriceWithoutTax());
        $this->assertEquals(1100, $price->getPriceWithTax());
        $this->assertEquals(100, $price->getTaxAmount());
        $this->assertEquals(0.1, $price->getTaxRate());
        $this->assertFalse($price->isFree());
    }

    public function test_軽減税率の価格計算が正しく行われる(): void
    {
        // 税抜1000円、軽減税率8%の商品
        $price = OrderItemPrice::withReducedTaxRate(1000);

        $this->assertEquals(1000, $price->getPriceWithoutTax());
        $this->assertEquals(1080, $price->getPriceWithTax());
        $this->assertEquals(80, $price->getTaxAmount());
        $this->assertEquals(0.08, $price->getTaxRate());
        $this->assertTrue($price->isReducedTaxRate());
        $this->assertFalse($price->isFree());
    }

    public function test_非課税の価格計算が正しく行われる(): void
    {
        // 税抜1000円、非課税の商品
        $price = OrderItemPrice::taxExempt(1000);

        $this->assertEquals(1000, $price->getPriceWithoutTax());
        $this->assertEquals(1000, $price->getPriceWithTax());
        $this->assertEquals(0, $price->getTaxAmount());
        $this->assertEquals(0.0, $price->getTaxRate());
        $this->assertFalse($price->isReducedTaxRate());
        $this->assertFalse($price->isFree());
    }

    public function test_無料商品の判定が正しく行われる(): void
    {
        // 税抜0円、税率10%の商品
        $freePrice = new OrderItemPrice(0, 0.1);

        $this->assertEquals(0, $freePrice->getPriceWithoutTax());
        $this->assertEquals(0, $freePrice->getPriceWithTax());
        $this->assertEquals(0, $freePrice->getTaxAmount());
        $this->assertTrue($freePrice->isFree());

        // 税抜0円、非課税の商品
        $freeTaxExemptPrice = OrderItemPrice::taxExempt(0);
        $this->assertTrue($freeTaxExemptPrice->isFree());
    }

    public function test_端数処理が正しく行われる(): void
    {
        // リフレクションを使用して定数値を取得
        $reflectionClass = new \ReflectionClass(Price::class);
        $roundingModeCeil = $reflectionClass->getConstant('ROUNDING_MODE_CEIL');
        $roundingModeFloor = $reflectionClass->getConstant('ROUNDING_MODE_FLOOR');

        // 四捨五入（デフォルト）
        $roundPrice = new OrderItemPrice(123, 0.1);
        $this->assertEquals(123, $roundPrice->getPriceWithoutTax());
        $this->assertEquals(135, $roundPrice->getPriceWithTax()); // 123 * 1.1 = 135.3 → 135（四捨五入）

        // 切り上げ
        $ceilPrice = new OrderItemPrice(123, 0.1, $roundingModeCeil);
        $this->assertEquals(123, $ceilPrice->getPriceWithoutTax());
        $this->assertEquals(136, $ceilPrice->getPriceWithTax()); // 123 * 1.1 = 135.3 → 136（切り上げ）

        // 切り捨て
        $floorPrice = new OrderItemPrice(123, 0.1, $roundingModeFloor);
        $this->assertEquals(123, $floorPrice->getPriceWithoutTax());
        $this->assertEquals(135, $floorPrice->getPriceWithTax()); // 123 * 1.1 = 135.3 → 135（切り捨て）
    }

    public function test_価格の加算が正しく行われる(): void
    {
        $price1 = new OrderItemPrice(1000, 0.1);
        $price2 = new OrderItemPrice(2000, 0.1);

        $sumPrice = $price1->add($price2);

        $this->assertEquals(3000, $sumPrice->getPriceWithoutTax());
        $this->assertEquals(3300, $sumPrice->getPriceWithTax());
        $this->assertEquals(300, $sumPrice->getTaxAmount());
    }

    public function test_異なる税率の価格は加算できない(): void
    {
        $price1 = new OrderItemPrice(1000, 0.1);
        $price2 = OrderItemPrice::withReducedTaxRate(2000);

        $this->expectException(\InvalidArgumentException::class);
        $price1->add($price2);
    }

    public function test_価格の比較が正しく行われる(): void
    {
        $price1 = new OrderItemPrice(1000, 0.1);
        $price2 = new OrderItemPrice(1000, 0.1);
        $price3 = new OrderItemPrice(2000, 0.1);
        $price4 = OrderItemPrice::withReducedTaxRate(1000);

        $this->assertTrue($price1->equals($price2));
        $this->assertFalse($price1->equals($price3)); // 金額が異なる
        $this->assertFalse($price1->equals($price4)); // 税率が異なる
    }

    public function test_文字列変換が正しく行われる(): void
    {
        $price = new OrderItemPrice(1000, 0.1);

        $this->assertEquals('1000', (string)$price);
    }

    public function test_デフォルト値が正しく設定される(): void
    {
        // デフォルト値（税率10%、四捨五入）
        $price = new OrderItemPrice(1000);

        $this->assertEquals(1000, $price->getPriceWithoutTax());
        $this->assertEquals(1100, $price->getPriceWithTax());
        $this->assertEquals(0.1, $price->getTaxRate());
    }
}
