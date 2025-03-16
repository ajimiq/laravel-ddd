<?php

namespace Tests\Unit\Order;

use PHPUnit\Framework\TestCase;
use App\Packages\Order\Domains\Entities\Order;
use App\Packages\Order\Domains\Entities\OrderItem;
use App\Packages\Order\Domains\Entities\OrderItems;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\Domains\ValueObjects\OrderStatus;
use App\Packages\Order\Domains\ValueObjects\ShippingFee;
use App\Packages\Order\Domains\ValueObjects\OrderCustomerInfo;
use App\Packages\Order\Domains\ValueObjects\OrderItemId;
use App\Packages\Order\Domains\ValueObjects\OrderItemName;
use App\Packages\Order\Domains\ValueObjects\OrderItemPrice;
use App\Packages\Shared\Domains\ValueObjects\EcSiteCode;
use DateTimeImmutable;

class OrderTest extends TestCase
{
    private Order $order;
    private OrderItems $orderItems;
    private ShippingFee $shippingFee;
    private OrderCustomerInfo $customerInfo;
    private DateTimeImmutable $orderedAt;
    private DateTimeImmutable $createdAt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderedAt = new DateTimeImmutable('2024-03-16 10:00:00');
        $this->createdAt = new DateTimeImmutable('2024-03-16 10:00:00');
        
        // OrderItemsのモックを作成
        $this->orderItems = $this->createStub(OrderItems::class);
        $this->shippingFee = new ShippingFee(800, 0.1);
        $this->customerInfo = new OrderCustomerInfo(
            '仮テスト太郎',
            'test.taro@example.com',
            '090-1234-5678',
            '東京都仮新宿区西新宿1-1-1'
        );

        $this->order = new Order(
            new OrderId('Order-20240316-001'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('pending'),
            $this->orderedAt,
            $this->shippingFee,
            $this->customerInfo,
            $this->orderItems,
            $this->createdAt
        );
    }

    public function test_注文ステータスが正しく判定される(): void
    {
        $this->assertTrue($this->order->isPending());
        $this->assertFalse($this->order->isFailure());
        
        // 失敗ステータスの注文を作成して検証
        $failedOrder = new Order(
            new OrderId('Order-20240316-002'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('failed'),
            $this->orderedAt,
            $this->shippingFee,
            $this->customerInfo,
            $this->orderItems,
            $this->createdAt
        );
        
        $this->assertFalse($failedOrder->isPending());
        $this->assertTrue($failedOrder->isFailure());
    }

    public function test_注文合計金額が正しく計算される(): void
    {
        // OrderItemsのモックを設定
        $this->orderItems->method('getSubtotalWithTax')
            ->willReturn(1100);
        $this->orderItems->method('getSubtotalWithoutTax')
            ->willReturn(1000);

        // 注文合計（税込）= 商品小計（税込）+ 送料（税込）
        $this->assertEquals(1980, $this->order->getTotalAmountWithTax()); // 1100 + 880

        // 注文合計（税抜）= 商品小計（税抜）+ 送料（税抜）
        $this->assertEquals(1800, $this->order->getTotalAmountWithoutTax()); // 1000 + 800
    }

    public function test_注文の税額が正しく計算される(): void
    {
        // OrderItemsのモックを設定
        $this->orderItems->method('getSubtotalWithTax')
            ->willReturn(1100);
        $this->orderItems->method('getSubtotalWithoutTax')
            ->willReturn(1000);

        // 税額 = 合計（税込）- 合計（税抜）
        $this->assertEquals(180, $this->order->getTotalTaxAmount()); // 1980 - 1800
    }

    public function test_注文IDが正しく取得できる(): void
    {
        $this->assertEquals('Order-20240316-001', $this->order->getOrderId()->getValue());
    }

    public function test_ECサイトコードが正しく取得できる(): void
    {
        $this->assertEquals('TEST-MALL', $this->order->getEcSiteCode()->getValue());
    }

    public function test_注文日時が正しく取得できる(): void
    {
        $this->assertEquals(
            '2024-03-16 10:00:00',
            $this->order->getOrderedAt()->format('Y-m-d H:i:s')
        );
    }

    public function test_顧客情報が正しく取得できる(): void
    {
        $customerInfo = $this->order->getCustomerInfo();
        
        $this->assertEquals('仮テスト太郎', $customerInfo->getName());
        $this->assertEquals('test.taro@example.com', $customerInfo->getEmail());
        $this->assertEquals('090-1234-5678', $customerInfo->getPhoneNumber());
        $this->assertEquals('東京都仮新宿区西新宿1-1-1', $customerInfo->getAddress());
    }

    public function test_送料が正しく取得できる(): void
    {
        $shippingFee = $this->order->getShippingFee();
        
        $this->assertEquals(800, $shippingFee->getPriceWithoutTax());
        $this->assertEquals(880, $shippingFee->getPriceWithTax());
        $this->assertEquals(0.1, $shippingFee->getTaxRate());
        $this->assertFalse($shippingFee->isFree());
    }

    public function test_送料無料の場合の動作確認(): void
    {
        // 送料無料の注文を作成
        $orderWithFreeShipping = new Order(
            new OrderId('Order-20240316-002'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('pending'),
            $this->orderedAt,
            ShippingFee::free(), // 送料無料
            $this->customerInfo,
            $this->orderItems,
            $this->createdAt
        );

        // OrderItemsのモックを設定
        $this->orderItems->method('getSubtotalWithTax')
            ->willReturn(1100);
        $this->orderItems->method('getSubtotalWithoutTax')
            ->willReturn(1000);

        // 送料が無料であることを確認
        $this->assertTrue($orderWithFreeShipping->getShippingFee()->isFree());
        
        // 注文合計が商品小計と等しいことを確認
        $this->assertEquals(1100, $orderWithFreeShipping->getTotalAmountWithTax());
        $this->assertEquals(1000, $orderWithFreeShipping->getTotalAmountWithoutTax());
        $this->assertEquals(100, $orderWithFreeShipping->getTotalTaxAmount());
    }

    public function test_注文の配列変換が正しく行われる(): void
    {
        // OrderItemsのモックを設定
        $this->orderItems->method('getSubtotalWithTax')
            ->willReturn(1100);
        $this->orderItems->method('getSubtotalWithoutTax')
            ->willReturn(1000);
        $this->orderItems->method('toArray')
            ->willReturn([
                [
                    'item_id' => 'item-001',
                    'name' => 'テスト商品',
                    'price_with_tax' => 1100,
                    'price_without_tax' => 1000,
                    'price_tax_rate' => 0.1,
                    'quantity' => 1,
                    'subtotal_with_tax' => 1100,
                    'subtotal_without_tax' => 1000,
                    'tax_amount' => 100
                ]
            ]);

        $orderArray = $this->order->toArray();
        
        $this->assertIsArray($orderArray);
        $this->assertEquals('Order-20240316-001', $orderArray['order_id']);
        $this->assertEquals('TEST-MALL', $orderArray['ec_site_code']);
        $this->assertEquals('pending', $orderArray['status']);
        $this->assertEquals(1980, $orderArray['total_amount_with_tax']);
        $this->assertEquals(1800, $orderArray['total_amount_without_tax']);
        $this->assertEquals('2024-03-16 10:00:00', $orderArray['ordered_at']);
        $this->assertIsArray($orderArray['order_items']);
    }

    public function test_キャンセル日時が正しく取得できる(): void
    {
        // キャンセル日時がnullの場合
        $this->assertNull($this->order->getCanceledAt());
        
        // キャンセル日時がある場合
        $canceledAt = new DateTimeImmutable('2024-03-17 10:00:00');
        $canceledOrder = new Order(
            new OrderId('Order-20240316-002'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('canceled'),
            $this->orderedAt,
            $this->shippingFee,
            $this->customerInfo,
            $this->orderItems,
            $this->createdAt,
            null,
            $canceledAt
        );
        
        $this->assertEquals(
            '2024-03-17 10:00:00',
            $canceledOrder->getCanceledAt()->format('Y-m-d H:i:s')
        );
    }

    public function test_税率ごとの税額が正しく計算される(): void
    {
        // 実際のOrderItemsを作成（モックではなく）
        $item1 = new OrderItem(
            new OrderItemId('item-001'),
            new OrderItemName('通常税率商品'),
            new OrderItemPrice(1000, 0.1), // 税抜1000円、税率10%
            1
        );
        
        $item2 = new OrderItem(
            new OrderItemId('item-002'),
            new OrderItemName('軽減税率商品'),
            OrderItemPrice::withReducedTaxRate(2000), // 税抜2000円、税率8%
            1
        );
        
        $realOrderItems = new OrderItems([$item1, $item2]);
        
        $orderWithRealItems = new Order(
            new OrderId('Order-20240316-003'),
            new EcSiteCode('TEST-MALL'),
            new OrderStatus('pending'),
            $this->orderedAt,
            $this->shippingFee, // 税抜800円、税率10%
            $this->customerInfo,
            $realOrderItems,
            $this->createdAt
        );
        
        $taxAmountsByRate = $orderWithRealItems->getTaxAmountsByRate();
        
        // 税率8%の集計
        $this->assertArrayHasKey('0.08', $taxAmountsByRate);
        $this->assertEquals(0.08, $taxAmountsByRate['0.08']['tax_rate']);
        $this->assertEquals(2160, $taxAmountsByRate['0.08']['subtotal_with_tax']); // 2000 * 1.08
        $this->assertEquals(2000, $taxAmountsByRate['0.08']['subtotal_without_tax']);
        $this->assertEquals(160, $taxAmountsByRate['0.08']['tax_amount']); // 2160 - 2000
        
        // 税率10%の集計（商品 + 送料）
        $this->assertArrayHasKey('0.1', $taxAmountsByRate);
        $this->assertEquals(0.1, $taxAmountsByRate['0.1']['tax_rate']);
        $this->assertEquals(1980, $taxAmountsByRate['0.1']['subtotal_with_tax']); // 1100 + 880
        $this->assertEquals(1800, $taxAmountsByRate['0.1']['subtotal_without_tax']); // 1000 + 800
        $this->assertEquals(180, $taxAmountsByRate['0.1']['tax_amount']); // 1980 - 1800
    }

    public function test_異なるステータスの注文が作成できる(): void
    {
        $statuses = [
            'pending' => true, // isPendingがtrue
            'unshipped' => false,
            'completed' => false,
            'failed' => true, // isFailureがtrue
            'canceled' => false,
        ];
        
        foreach ($statuses as $statusValue => $expectedFailure) {
            $order = new Order(
                new OrderId('Order-20240316-' . $statusValue),
                new EcSiteCode('TEST-MALL'),
                new OrderStatus($statusValue),
                $this->orderedAt,
                $this->shippingFee,
                $this->customerInfo,
                $this->orderItems,
                $this->createdAt
            );
            
            if ($statusValue === 'pending') {
                $this->assertTrue($order->isPending(), "ステータス '{$statusValue}' の注文はpendingと判定されるべき");
            } else {
                $this->assertFalse($order->isPending(), "ステータス '{$statusValue}' の注文はpendingと判定されるべきではない");
            }
            
            if ($statusValue === 'failed') {
                $this->assertTrue($order->isFailure(), "ステータス '{$statusValue}' の注文はfailureと判定されるべき");
            } else {
                $this->assertFalse($order->isFailure(), "ステータス '{$statusValue}' の注文はfailureと判定されるべきではない");
            }
        }
    }
}
