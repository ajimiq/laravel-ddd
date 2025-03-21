<?php

namespace App\Packages\Order\Domains\Entities;

use DateTimeImmutable;
use App\Packages\Order\Domains\Entities\OrderItems;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\Domains\ValueObjects\OrderStatus;
use App\Packages\Order\Domains\ValueObjects\ShippingFee;
use App\Packages\Order\Domains\ValueObjects\OrderCustomerInfo;
use App\Packages\Shared\Domains\ValueObjects\EcSiteCode;

class Order
{
    private OrderId $orderId;
    private EcSiteCode $ecSiteCode;
    private OrderStatus $status;
    private DateTimeImmutable $orderedAt;
    private ShippingFee $shippingFee;
    private OrderCustomerInfo $customerInfo;
    private OrderItems $orderItems;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $canceledAt;

    public function __construct(
        OrderId $orderId,
        EcSiteCode $ecSiteCode,
        OrderStatus $status,
        DateTimeImmutable $orderedAt,
        ShippingFee $shippingFee,
        OrderCustomerInfo $customerInfo,
        OrderItems $orderItems,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt = null,
        ?DateTimeImmutable $canceledAt = null
    ) {
        $this->orderId = $orderId;
        $this->ecSiteCode = $ecSiteCode;
        $this->status = $status;
        $this->orderedAt = $orderedAt;
        $this->shippingFee = $shippingFee;
        $this->customerInfo = $customerInfo;
        $this->orderItems = $orderItems;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->canceledAt = $canceledAt;
    }

    /**
     * 注文IDを取得
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * 注文ステータスを取得
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * 送料を取得
     */
    public function getShippingFee(): ShippingFee
    {
        return $this->shippingFee;
    }

    /**
     * 顧客情報を取得
     */
    public function getCustomerInfo(): OrderCustomerInfo
    {
        return $this->customerInfo;
    }

    /**
     * 商品リストを取得
     */
    public function getOrderItems(): OrderItems
    {
        return $this->orderItems;
    }

    /**
     * 注文日時を取得
     */
    public function getOrderedAt(): DateTimeImmutable
    {
        return $this->orderedAt;
    }

    /**
     * キャンセル日時を取得
     */
    public function getCanceledAt(): ?DateTimeImmutable
    {
        return $this->canceledAt;
    }

    /**
     * 保留中かどうかを確認
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    /**
     * 失敗したかどうかを確認
     */
    public function isFailure(): bool
    {
        return $this->status->isFailure();
    }

    /**
     * 注文合計を取得
     * 商品小計 + 送料
     */
    public function getTotalAmountWithTax(): int
    {
        return $this->orderItems->getSubtotalWithTax() + $this->shippingFee->getPriceWithTax();
    }

    /**
     * 注文合計（税抜）を取得
     * 商品小計（税抜） + 送料（税抜）
     */
    public function getTotalAmountWithoutTax(): int
    {
        return $this->orderItems->getSubtotalWithoutTax() + $this->shippingFee->getPriceWithoutTax();
    }

    /**
     * 注文の税額合計を取得
     */
    public function getTotalTaxAmount(): int
    {
        return $this->getTotalAmountWithTax() - $this->getTotalAmountWithoutTax();
    }

    /**
     * ECサイトコードを取得
     */
    public function getEcSiteCode(): EcSiteCode
    {
        return $this->ecSiteCode;
    }

    /**
     * 注文を配列に変換
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId->getValue(),
            'ec_site_code' => $this->ecSiteCode->getValue(),
            'status' => $this->status->getValue(),
            'total_amount_with_tax' => $this->getTotalAmountWithTax(),
            'total_amount_without_tax' => $this->getTotalAmountWithoutTax(),
            'order_items' => $this->orderItems->toArray(),
            'ordered_at' => $this->orderedAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'canceled_at' => $this->canceledAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * 消費税率ごとの税額を取得
     *
     * @return array<float, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }>
     */
    public function getTaxAmountsByRate(): array
    {
        // 商品の税額を税率ごとに集計
        $itemTaxes = [];
        foreach ($this->getOrderItems() as $item) {
            $taxRate = (string)$item->getPrice()->getTaxRate();
            if (!isset($itemTaxes[$taxRate])) {
                $itemTaxes[$taxRate] = [
                    'tax_rate' => $taxRate,
                    'subtotal_with_tax' => 0,
                    'subtotal_without_tax' => 0,
                    'tax_amount' => 0,
                ];
            }

            $itemTaxes[$taxRate]['subtotal_with_tax'] += $item->getSubtotalWithTax();
            $itemTaxes[$taxRate]['subtotal_without_tax'] += $item->getSubtotalWithoutTax();
            $itemTaxes[$taxRate]['tax_amount'] += $item->getTaxAmount();
        }

        // 送料の税額を追加
        $shippingFee = $this->getShippingFee();
        $shippingTaxRate = (string)$shippingFee->getTaxRate();
        if (!isset($itemTaxes[$shippingTaxRate])) {
            $itemTaxes[$shippingTaxRate] = [
                'tax_rate' => $shippingTaxRate,
                'subtotal_with_tax' => 0,
                'subtotal_without_tax' => 0,
                'tax_amount' => 0,
            ];
        }

        $itemTaxes[$shippingTaxRate]['subtotal_with_tax'] += $shippingFee->getPriceWithTax();
        $itemTaxes[$shippingTaxRate]['subtotal_without_tax'] += $shippingFee->getPriceWithoutTax();
        $itemTaxes[$shippingTaxRate]['tax_amount'] +=
            $shippingFee->getPriceWithTax() - $shippingFee->getPriceWithoutTax();

        // 税率の昇順でソート
        ksort($itemTaxes);

        return $itemTaxes;
    }
}
