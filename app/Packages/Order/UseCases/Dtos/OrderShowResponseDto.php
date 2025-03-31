<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 注文詳細の結果DTO
 */
class OrderShowResponseDto
{
    /**
    /**
     * @param array{
     *   order_id: string,
     *   status: string,
     *   ordered_at: string,
     *   customer_info: array{
     *     customer_name: string,
     *     customer_email: string,
     *     customer_phone: string,
     *     customer_address: string
     *   },
     *   shipping_fee_with_tax: int,
     *   shipping_fee_without_tax: int,
     *   shipping_fee_tax_rate: float,
     *   total_amount_with_tax: int,
     *   total_amount_without_tax: int,
     *   order_items: array<int, array{
     *     item_id: string,
     *     name: string,
     *     price_with_tax: int,
     *     price_without_tax: int,
     *     price_tax_rate: float,
     *     quantity: int,
     *     subtotal_with_tax: int,
     *     subtotal_without_tax: int
     *   }>
     * } $order 注文データ
     * @param array<float|string, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }> $taxAmountsByRate 税率ごとの金額
     * @param array<string, string> $statuses ステータス一覧
     */
    public function __construct(
        private readonly array $order,
        private readonly array $taxAmountsByRate,
        private readonly array $statuses
    ) {
    }

    /**
     * 注文データを取得
     *
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * 税率ごとの金額を取得
     *
     * @return array<float|string, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }>
     */
    public function getTaxAmountsByRate(): array
    {
        return $this->taxAmountsByRate;
    }

    /**
     * ステータス一覧を取得
     *
     * @return array<string, string>
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * ビューに渡すデータを取得
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'order' => $this->order,
            'taxAmountsByRate' => $this->taxAmountsByRate,
            'statuses' => $this->statuses,
        ];
    }
}
