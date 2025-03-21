<?php

namespace Tests\Unit\Order;

use PHPUnit\Framework\TestCase;
use App\Packages\Order\Domains\Entities\OrderItem;
use App\Packages\Order\Domains\ValueObjects\OrderItemId;
use App\Packages\Order\Domains\ValueObjects\OrderItemName;
use App\Packages\Order\Domains\ValueObjects\OrderItemPrice;

class OrderItemTest extends TestCase
{
    private OrderItem $orderItem;
    private OrderItemId $itemId;
    private OrderItemName $name;
    private OrderItemPrice $price;
    private int $quantity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemId = new OrderItemId('item-001');
        $this->name = new OrderItemName('テスト商品');
        $this->price = new OrderItemPrice(1000, 0.1); // 税抜1000円、税率10%
        $this->quantity = 2;

        $this->orderItem = new OrderItem(
            $this->itemId,
            $this->name,
            $this->price,
            $this->quantity
        );
    }

    public function test_商品IDが正しく取得できる(): void
    {
        $this->assertEquals('item-001', $this->orderItem->getItemId()->getValue());
    }

    public function test_商品名が正しく取得できる(): void
    {
        $this->assertEquals('テスト商品', $this->orderItem->getName()->getValue());
    }

    public function test_価格が正しく取得できる(): void
    {
        $price = $this->orderItem->getPrice();
        $this->assertEquals(1000, $price->getPriceWithoutTax());
        $this->assertEquals(1100, $price->getPriceWithTax());
        $this->assertEquals(0.1, $price->getTaxRate());
    }

    public function test_数量が正しく取得できる(): void
    {
        $this->assertEquals(2, $this->orderItem->getQuantity());
    }

    public function test_小計税込が正しく計算される(): void
    {
        // 税込価格 1100円 × 数量 2個 = 2200円
        $this->assertEquals(2200, $this->orderItem->getSubtotalWithTax());
    }

    public function test_小計税抜が正しく計算される(): void
    {
        // 税抜価格 1000円 × 数量 2個 = 2000円
        $this->assertEquals(2000, $this->orderItem->getSubtotalWithoutTax());
    }

    public function test_税額が正しく計算される(): void
    {
        // 税込小計 2200円 - 税抜小計 2000円 = 200円
        $this->assertEquals(200, $this->orderItem->getTaxAmount());
    }

    public function test_配列変換が正しく行われる(): void
    {
        $array = $this->orderItem->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('item-001', $array['item_id']);
        $this->assertEquals('テスト商品', $array['name']);
        $this->assertEquals(1100, $array['price_with_tax']);
        $this->assertEquals(1000, $array['price_without_tax']);
        $this->assertEquals(0.1, $array['price_tax_rate']);
        $this->assertEquals(2, $array['quantity']);
        $this->assertEquals(2200, $array['subtotal_with_tax']);
        $this->assertEquals(2000, $array['subtotal_without_tax']);
        $this->assertEquals(200, $array['tax_amount']);
    }

    public function test_軽減税率商品の計算が正しく行われる(): void
    {
        $reducedTaxPrice = OrderItemPrice::withReducedTaxRate(1000); // 税抜1000円、軽減税率8%
        $reducedTaxItem = new OrderItem(
            $this->itemId,
            $this->name,
            $reducedTaxPrice,
            $this->quantity
        );

        // 税込価格 1080円 × 数量 2個 = 2160円
        $this->assertEquals(2160, $reducedTaxItem->getSubtotalWithTax());
        // 税抜価格 1000円 × 数量 2個 = 2000円
        $this->assertEquals(2000, $reducedTaxItem->getSubtotalWithoutTax());
        // 税額 2160円 - 2000円 = 160円
        $this->assertEquals(160, $reducedTaxItem->getTaxAmount());
    }

    public function test_非課税商品の計算が正しく行われる(): void
    {
        $taxExemptPrice = OrderItemPrice::taxExempt(1000); // 税抜1000円、非課税
        $taxExemptItem = new OrderItem(
            $this->itemId,
            $this->name,
            $taxExemptPrice,
            $this->quantity
        );

        // 非課税なので税込価格 = 税抜価格
        $this->assertEquals(2000, $taxExemptItem->getSubtotalWithTax());
        $this->assertEquals(2000, $taxExemptItem->getSubtotalWithoutTax());
        $this->assertEquals(0, $taxExemptItem->getTaxAmount());
    }
}
