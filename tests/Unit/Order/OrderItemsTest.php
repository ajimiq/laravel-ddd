<?php

namespace Tests\Unit\Order;

use PHPUnit\Framework\TestCase;
use App\Packages\Order\Domains\Entities\OrderItem;
use App\Packages\Order\Domains\Entities\OrderItems;
use App\Packages\Order\Domains\ValueObjects\OrderItemId;
use App\Packages\Order\Domains\ValueObjects\OrderItemName;
use App\Packages\Order\Domains\ValueObjects\OrderItemPrice;

class OrderItemsTest extends TestCase
{
    private OrderItems $orderItems;
    private OrderItem $item1;
    private OrderItem $item2;

    protected function setUp(): void
    {
        parent::setUp();

        // 商品1: 税抜1000円、税率10%、数量2個
        $this->item1 = new OrderItem(
            new OrderItemId('item-001'),
            new OrderItemName('テスト商品1'),
            new OrderItemPrice(1000, 0.1),
            2
        );

        // 商品2: 税抜2000円、軽減税率8%、数量1個
        $this->item2 = new OrderItem(
            new OrderItemId('item-002'),
            new OrderItemName('テスト商品2'),
            OrderItemPrice::withReducedTaxRate(2000),
            1
        );

        // 空のコレクションを作成
        $this->orderItems = new OrderItems();
    }

    public function test_商品を追加できる(): void
    {
        $this->orderItems->addItem($this->item1);
        $this->orderItems->addItem($this->item2);

        // Iteratorの実装をテスト
        $items = [];
        foreach ($this->orderItems as $item) {
            $items[] = $item;
        }

        $this->assertCount(2, $items);
        $this->assertSame($this->item1, $items[0]);
        $this->assertSame($this->item2, $items[1]);
    }

    public function test_商品小計税込が正しく計算される(): void
    {
        $this->orderItems->addItem($this->item1);
        $this->orderItems->addItem($this->item2);

        // 商品1: 1100円 × 2個 = 2200円
        // 商品2: 2160円 × 1個 = 2160円
        // 合計: 4360円
        $this->assertEquals(4360, $this->orderItems->getSubtotalWithTax());
    }

    public function test_商品小計税抜が正しく計算される(): void
    {
        $this->orderItems->addItem($this->item1);
        $this->orderItems->addItem($this->item2);

        // 商品1: 1000円 × 2個 = 2000円
        // 商品2: 2000円 × 1個 = 2000円
        // 合計: 4000円
        $this->assertEquals(4000, $this->orderItems->getSubtotalWithoutTax());
    }

    public function test_商品の税額合計が正しく計算される(): void
    {
        $this->orderItems->addItem($this->item1);
        $this->orderItems->addItem($this->item2);

        // 商品1: 税額 200円
        // 商品2: 税額 160円
        // 合計: 360円
        $this->assertEquals(360, $this->orderItems->getTotalTaxAmount());
    }

    public function test_空のコレクションの場合は0が返される(): void
    {
        $this->assertEquals(0, $this->orderItems->getSubtotalWithTax());
        $this->assertEquals(0, $this->orderItems->getSubtotalWithoutTax());
        $this->assertEquals(0, $this->orderItems->getTotalTaxAmount());
    }

    public function test_配列変換が正しく行われる(): void
    {
        $this->orderItems->addItem($this->item1);
        $this->orderItems->addItem($this->item2);

        $array = $this->orderItems->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 商品1の検証
        $this->assertEquals('item-001', $array[0]['item_id']);
        $this->assertEquals('テスト商品1', $array[0]['name']);
        $this->assertEquals(1100, $array[0]['price_with_tax']);
        $this->assertEquals(1000, $array[0]['price_without_tax']);
        $this->assertEquals(0.1, $array[0]['price_tax_rate']);
        $this->assertEquals(2, $array[0]['quantity']);
        $this->assertEquals(2200, $array[0]['subtotal_with_tax']);
        $this->assertEquals(2000, $array[0]['subtotal_without_tax']);
        $this->assertEquals(200, $array[0]['tax_amount']);

        // 商品2の検証
        $this->assertEquals('item-002', $array[1]['item_id']);
        $this->assertEquals('テスト商品2', $array[1]['name']);
        $this->assertEquals(2160, $array[1]['price_with_tax']);
        $this->assertEquals(2000, $array[1]['price_without_tax']);
        $this->assertEquals(0.08, $array[1]['price_tax_rate']);
        $this->assertEquals(1, $array[1]['quantity']);
        $this->assertEquals(2160, $array[1]['subtotal_with_tax']);
        $this->assertEquals(2000, $array[1]['subtotal_without_tax']);
        $this->assertEquals(160, $array[1]['tax_amount']);
    }

    public function test_イテレータの実装が正しく動作する(): void
    {
        $this->orderItems->addItem($this->item1);
        $this->orderItems->addItem($this->item2);

        // rewind()とcurrent()のテスト
        $this->orderItems->rewind();
        $this->assertSame($this->item1, $this->orderItems->current());
        $this->assertEquals(0, $this->orderItems->key());

        // next()のテスト
        $this->orderItems->next();
        $this->assertSame($this->item2, $this->orderItems->current());
        $this->assertEquals(1, $this->orderItems->key());

        // valid()のテスト
        $this->assertTrue($this->orderItems->valid());
        $this->orderItems->next();
        $this->assertFalse($this->orderItems->valid());
    }

    public function test_コンストラクタで商品を初期化できる(): void
    {
        $items = [$this->item1, $this->item2];
        $orderItems = new OrderItems($items);

        $this->assertEquals(4360, $orderItems->getSubtotalWithTax());
        $this->assertEquals(4000, $orderItems->getSubtotalWithoutTax());
        $this->assertEquals(360, $orderItems->getTotalTaxAmount());
    }
}
