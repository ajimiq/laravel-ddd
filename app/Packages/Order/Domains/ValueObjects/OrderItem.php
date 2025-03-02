<?php

namespace App\Packages\Order\Domains\ValueObjects;

class OrderItem
{
    public function __construct(
        private readonly OrderItemId $itemId,
        private readonly OrderItemName $name,
        private readonly OrderItemPrice $price,
        private readonly int $quantity
    ) {
    }

    /**
     * 商品IDを取得
     */
    public function getItemId(): OrderItemId
    {
        return $this->itemId;
    }

    /**
     * 商品名を取得
     */
    public function getName(): OrderItemName
    {
        return $this->name;
    }

    /**
     * 価格を取得
     */
    public function getPrice(): OrderItemPrice
    {
        return $this->price;
    }

    /**
     * 数量を取得
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * 小計（税込）を取得
     */
    public function getSubtotalWithTax(): int
    {
        return $this->price->getPriceWithTax() * $this->quantity;
    }

    /**
     * 小計（税抜）を取得
     */
    public function getSubtotalWithoutTax(): int
    {
        return $this->price->getPriceWithoutTax() * $this->quantity;
    }

    /**
     * 税額を取得
     */
    public function getTaxAmount(): int
    {
        return $this->getSubtotalWithTax() - $this->getSubtotalWithoutTax();
    }

    /**
     * 配列に変換
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'item_id' => $this->itemId->getValue(),
            'name' => $this->name->getValue(),
            'price_with_tax' => $this->price->getPriceWithTax(),
            'price_without_tax' => $this->price->getPriceWithoutTax(),
            'price_tax_rate' => $this->price->getTaxRate(),
            'quantity' => $this->quantity,
            'subtotal_with_tax' => $this->getSubtotalWithTax(),
            'subtotal_without_tax' => $this->getSubtotalWithoutTax(),
            'tax_amount' => $this->getTaxAmount(),
        ];
    }
}