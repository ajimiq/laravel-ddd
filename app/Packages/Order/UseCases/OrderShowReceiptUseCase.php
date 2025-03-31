<?php

namespace App\Packages\Order\UseCases;

use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\UseCases\Dtos\OrderShowReceiptRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderShowReceiptResponseDto;
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
        // リポジトリから注文エンティティを取得
        $orderId = new OrderId($requestDto->getOrderId());
        $order = $this->orderRepository->find($orderId);
        
        if (!$order) {
            throw new \RuntimeException('注文が見つかりませんでした。');
        }

        // 税率ごとの金額を取得
        $taxAmountsByRate = $order->getTaxAmountsByRate();

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
            'number' => 'R' . $order->getOrderId()->getValue(),
            'issue_date' => new DateTimeImmutable(),
        ];

        // エンティティから注文データを配列に変換
        $orderData = [
            'order_id' => $order->getOrderId()->getValue(),
            'status' => $order->getStatus()->getValue(),
            'ordered_at' => $order->getOrderedAt()->format('Y-m-d H:i:s'),
            'customer_info' => [
                'customer_name' => $order->getCustomerInfo()->getName(),
                'customer_email' => $order->getCustomerInfo()->getEmail(),
                'customer_phone' => $order->getCustomerInfo()->getPhoneNumber(),
                'customer_address' => $order->getCustomerInfo()->getAddress(),
            ],
            'shipping_fee_with_tax' => $order->getShippingFee()->getPriceWithTax(),
            'shipping_fee_without_tax' => $order->getShippingFee()->getPriceWithoutTax(),
            'shipping_fee_tax_rate' => $order->getShippingFee()->getTaxRate(),
            'total_amount_with_tax' => $order->getTotalAmountWithTax(),
            'total_amount_without_tax' => $order->getTotalAmountWithoutTax(),
            'order_items' => []
        ];

        // 注文商品情報を追加
        foreach ($order->getOrderItems() as $item) {
            $orderData['order_items'][] = [
                'item_id' => $item->getItemId()->getValue(),
                'name' => $item->getName()->getValue(),
                'price_with_tax' => $item->getPrice()->getPriceWithTax(),
                'price_without_tax' => $item->getPrice()->getPriceWithoutTax(), 
                'price_tax_rate' => $item->getPrice()->getTaxRate(),
                'quantity' => $item->getQuantity(),
                'subtotal_with_tax' => $item->getSubtotalWithTax(),
                'subtotal_without_tax' => $item->getSubtotalWithoutTax()
            ];
        }

        return new OrderShowReceiptResponseDto(
            $orderData,
            $company,
            $receipt,
            $taxAmountsByRate
        );
    }
}
