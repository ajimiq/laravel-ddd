<?php

namespace App\Packages\Order\UseCases;

use App\Models\Order as OrderModel;
use App\Packages\Order\Domains\OrderRepositoryInterface;
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
use App\Packages\Order\UseCases\Dtos\OrderShowReceiptRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderShowReceiptResponseDto;
use App\Packages\Shared\Domains\ValueObjects\EcSiteCode;
use DateTimeImmutable;
use Illuminate\Support\Facades\Config;

class OrderShowReceiptUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * 領収書データを取得
     * 
     * @param OrderShowReceiptRequestDto $requestDto
     * @return OrderShowReceiptResponseDto
     */
    public function execute(OrderShowReceiptRequestDto $requestDto): OrderShowReceiptResponseDto
    {
        // Eloquentモデルとして注文を取得
        $orderModel = OrderModel::with(['orderItems' => function($query) {
            $query->orderBy('created_at');
        }])->findOrFail($requestDto->getOrderId());

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
        $taxAmountsByRate = $order->getTaxAmountsByRate($order);

        // 会社情報
        $company = [
            'name' => Config::get('company.name'),
            'postal_code' => Config::get('company.postal_code'),
            'address' => Config::get('company.address'),
            'tel' => Config::get('company.tel'),
            'registration_number' => Config::get('company.registration_number'),
        ];

        // 領収書情報
        $receipt = [
            'number' => 'R' . $orderModel->order_id,
            'issue_date' => new DateTimeImmutable(),
        ];

        return new OrderShowReceiptResponseDto(
            $orderModel,
            $company,
            $receipt,
            $taxAmountsByRate
        );
    }
} 