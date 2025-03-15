<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Packages\Order\Domains\Entities\OrderItem;
use App\Packages\Order\Domains\Entities\OrderItems;
use App\Packages\Order\Domains\ValueObjects\OrderItemId;
use App\Packages\Order\Domains\ValueObjects\OrderItemName;
use App\Packages\Order\Domains\ValueObjects\OrderItemPrice;

class OrderItemsTest extends TestCase
{
    use RefreshDatabase;

    private OrderItems $orderItems;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderItems = new OrderItems([
            new OrderItem(
                new OrderItemId('ITEM-001'),
                new OrderItemName('テスト商品1'),
                new OrderItemPrice(1000, 0.10),
                2
            ),
            new OrderItem(
                new OrderItemId('ITEM-002'),
                new OrderItemName('テスト商品2'),
                new OrderItemPrice(500, 0.08),
                1
            )
        ]);
    }

    public function test_商品小計が正しく計算される(): void
    {
        // 商品1: 1000円 × 2個 × 1.10 = 2200円
        // 商品2: 500円 × 1個 × 1.08 = 540円
        $this->assertEquals(2740, $this->orderItems->getSubtotalWithTax());

        // 商品1: 1000円 × 2個 = 2000円
        // 商品2: 500円 × 1個 = 500円
        $this->assertEquals(2500, $this->orderItems->getSubtotalWithoutTax());
    }

    public function test_商品リストを配列に変換できる(): void
    {
        $array = $this->orderItems->toArray();

        $this->assertCount(2, $array);
        $this->assertEquals('ITEM-001', $array[0]['item_id']);
        $this->assertEquals('テスト商品1', $array[0]['name']);
        $this->assertEquals(1000, $array[0]['price_without_tax']);
        $this->assertEquals(0.10, $array[0]['price_tax_rate']);
        $this->assertEquals(2, $array[0]['quantity']);
    }
} 