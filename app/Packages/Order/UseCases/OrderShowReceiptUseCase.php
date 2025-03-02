<?php

namespace App\Packages\Order\UseCases;

use App\Models\Order as OrderModel;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\Domains\Services\InvoiceService;
use App\Packages\Order\Domains\ValueObjects\Order;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\Domains\ValueObjects\OrderStatus;
use App\Packages\Order\Domains\ValueObjects\ShippingFee;
use App\Packages\Order\Domains\ValueObjects\OrderCustomerInfo;
use App\Packages\Order\Domains\ValueObjects\OrderItems;
use App\Packages\Order\Domains\ValueObjects\OrderItem;
use App\Packages\Order\Domains\ValueObjects\OrderItemId;
use App\Packages\Order\Domains\ValueObjects\OrderItemName;
use App\Packages\Order\Domains\ValueObjects\OrderItemPrice;
use App\Packages\Shared\Domains\ValueObjects\EcSiteCode;
use DateTimeImmutable;
use Illuminate\Support\Facades\Config;

class OrderShowReceiptUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly InvoiceService $invoiceService
    ) {
    }

    /**
     * 領収書データを取得
     * 
     * @return array{
     *   order: OrderModel,
     *   company: array{
     *     name: string,
     *     postal_code: string,
     *     address: string,
     *     tel: string,
     *     registration_number: string
     *   },
     *   receipt: array{
     *     number: string,
     *     issue_date: \DateTimeImmutable
     *   },
     *   tax_amounts_by_rate: array<float, array{
     *     tax_rate: float,
     *     subtotal_with_tax: int,
     *     subtotal_without_tax: int,
     *     tax_amount: int
     *   }>
     * }
     */
    public function execute(string $orderId): array
    {
        // Eloquentモデルとして注文を取得
        $orderModel = OrderModel::with(['orderItems' => function($query) {
            $query->orderBy('created_at');
        }])->findOrFail($orderId);

        // 注文商品の変換
        $orderItems = new OrderItems(
            array_map(
                fn($item) => new OrderItem(
                    new OrderItemId($item->item_id),
                    new OrderItemName($item->name),
                    new OrderItemPrice(
                        $item->price_without_tax,
                        $item->price_tax_rate
                    ),
                    $item->quantity
                ),
                $orderModel->orderItems->all()
            )
        );

        // 注文の変換
        $order = new Order(
            new OrderId($orderModel->order_id),
            new EcSiteCode($orderModel->ec_site_code),
            new OrderStatus($orderModel->status),
            new DateTimeImmutable($orderModel->ordered_at),
            new ShippingFee(
                $orderModel->shipping_fee_without_tax,
                $orderModel->shipping_fee_tax_rate
            ),
            new OrderCustomerInfo(
                $orderModel->customer_name,
                $orderModel->customer_email,
                $orderModel->customer_phone,
                $orderModel->customer_address
            ),
            $orderItems,
            new DateTimeImmutable($orderModel->created_at),
            $orderModel->updated_at ? new DateTimeImmutable($orderModel->updated_at) : null,
            $orderModel->canceled_at ? new DateTimeImmutable($orderModel->canceled_at) : null
        );

        // 税率ごとの金額を取得
        $taxAmountsByRate = $this->invoiceService->getTaxAmountsByRate($order);

        // 会社情報
        $company = [
            'name' => Config::get('app.company_name', '株式会社サンプル'),
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区サンプル1-2-3',
            'tel' => '03-1234-5678',
            'registration_number' => 'T1234567890123',
        ];

        // 領収書情報
        $now = new DateTimeImmutable();
        $receipt = [
            'number' => sprintf('R%s-%s', 
                $now->format('Ymd'), 
                substr($orderId, -4)
            ),
            'issue_date' => $now,
        ];

        return [
            'order' => $orderModel,
            'company' => $company,
            'receipt' => $receipt,
            'tax_amounts_by_rate' => $taxAmountsByRate,
        ];
    }
} 