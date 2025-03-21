<?php

namespace Tests\Unit\Order;

use PHPUnit\Framework\TestCase;
use App\Packages\Order\Domains\Entities\Order;
use App\Packages\Order\Domains\Entities\Orders;
use App\Packages\Order\Domains\Entities\OrderItems;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\Domains\ValueObjects\OrderStatus;
use App\Packages\Order\Domains\ValueObjects\ShippingFee;
use App\Packages\Order\Domains\ValueObjects\OrderCustomerInfo;
use App\Packages\Shared\Domains\ValueObjects\EcSiteCode;
use DateTimeImmutable;

class OrdersTest extends TestCase
{
    private Orders $orders;
    private Order $order1;
    private Order $order2;

    protected function setUp(): void
    {
        parent::setUp();

        // モック用のOrderItemsを作成
        $orderItems1 = $this->createStub(OrderItems::class);
        $orderItems2 = $this->createStub(OrderItems::class);

        // 注文1
        $this->order1 = new Order(
            new OrderId('Order-20240316-001'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('pending'),
            new DateTimeImmutable('2024-03-16 10:00:00'),
            new ShippingFee(800, 0.1),
            new OrderCustomerInfo(
                'テスト太郎',
                'test.taro@example.com',
                '090-1234-5678',
                '東京都新宿区西新宿1-1-1'
            ),
            $orderItems1,
            new DateTimeImmutable('2024-03-16 10:00:00')
        );

        // 注文2
        $this->order2 = new Order(
            new OrderId('Order-20240316-002'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('completed'),
            new DateTimeImmutable('2024-03-16 11:00:00'),
            new ShippingFee(500, 0.1),
            new OrderCustomerInfo(
                'テスト花子',
                'test.hanako@example.com',
                '090-8765-4321',
                '東京都渋谷区渋谷1-1-1'
            ),
            $orderItems2,
            new DateTimeImmutable('2024-03-16 11:00:00')
        );

        // 空のコレクションを作成
        $this->orders = new Orders();
    }

    public function test_注文を追加できる(): void
    {
        $this->orders->add($this->order1);
        $this->orders->add($this->order2);

        // Iteratorの実装をテスト
        $items = [];
        foreach ($this->orders as $order) {
            $items[] = $order;
        }

        $this->assertCount(2, $items);
        $this->assertSame($this->order1, $items[0]);
        $this->assertSame($this->order2, $items[1]);
    }

    public function test_配列に変換できる(): void
    {
        $this->orders->add($this->order1);
        $this->orders->add($this->order2);

        $array = $this->orders->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertSame($this->order1, $array[0]);
        $this->assertSame($this->order2, $array[1]);
    }

    public function test_空かどうかを判定できる(): void
    {
        $this->assertTrue($this->orders->isEmpty());

        $this->orders->add($this->order1);
        $this->assertFalse($this->orders->isEmpty());
    }

    public function test_コンストラクタで注文を初期化できる(): void
    {
        $ordersArray = [$this->order1, $this->order2];
        $orders = new Orders($ordersArray);

        $this->assertCount(2, $orders);
        $this->assertSame($this->order1, $orders[0]);
        $this->assertSame($this->order2, $orders[1]);
    }

    public function test_Countableインターフェースが実装されている(): void
    {
        $this->assertEquals(0, count($this->orders));

        $this->orders->add($this->order1);
        $this->assertEquals(1, count($this->orders));

        $this->orders->add($this->order2);
        $this->assertEquals(2, count($this->orders));
    }

    public function test_ArrayAccessインターフェースが実装されている(): void
    {
        // offsetSet
        $this->orders[] = $this->order1;
        $this->orders[1] = $this->order2;

        // offsetExists
        $this->assertTrue(isset($this->orders[0]));
        $this->assertTrue(isset($this->orders[1]));
        $this->assertFalse(isset($this->orders[2]));

        // offsetGet
        $this->assertSame($this->order1, $this->orders[0]);
        $this->assertSame($this->order2, $this->orders[1]);
        $this->assertNull($this->orders[2]);

        // offsetUnset
        unset($this->orders[0]);
        $this->assertFalse(isset($this->orders[0]));
        $this->assertTrue(isset($this->orders[1]));
    }

    public function test_イテレータの実装が正しく動作する(): void
    {
        $this->orders->add($this->order1);
        $this->orders->add($this->order2);

        // rewind()とcurrent()のテスト
        $this->orders->rewind();
        $this->assertSame($this->order1, $this->orders->current());
        $this->assertEquals(0, $this->orders->key());

        // next()のテスト
        $this->orders->next();
        $this->assertSame($this->order2, $this->orders->current());
        $this->assertEquals(1, $this->orders->key());

        // valid()のテスト
        $this->assertTrue($this->orders->valid());
        $this->orders->next();
        $this->assertFalse($this->orders->valid());
    }
}
