<?php

namespace App\Packages\Order\UseCases\Dtos;

use App\Models\Order;

/**
 * 領収書表示結果DTO
 */
class OrderShowReceiptResponseDto
{
    /**
     * @param Order $order 注文
     * @param array{
     *   name: string,
     *   postal_code: string,
     *   address: string,
     *   tel: string,
     *   registration_number: string
     * } $company 会社情報
     * @param array{
     *   number: string,
     *   issue_date: \DateTimeImmutable
     * } $receipt 領収書情報
     * @param array<float, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }> $taxAmountsByRate 税率ごとの金額
     */
    public function __construct(
        private readonly Order $order,
        private readonly array $company,
        private readonly array $receipt,
        private readonly array $taxAmountsByRate
    ) {
    }

    /**
     * 注文を取得
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * 会社情報を取得
     *
     * @return array{
     *   name: string,
     *   postal_code: string,
     *   address: string,
     *   tel: string,
     *   registration_number: string
     * }
     */
    public function getCompany(): array
    {
        return $this->company;
    }

    /**
     * 領収書情報を取得
     *
     * @return array{
     *   number: string,
     *   issue_date: \DateTimeImmutable
     * }
     */
    public function getReceipt(): array
    {
        return $this->receipt;
    }

    /**
     * 税率ごとの金額を取得
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
        return $this->taxAmountsByRate;
    }

    /**
     * ビューに渡すデータを取得
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'order' => $this->order,
            'company' => $this->company,
            'receipt' => $this->receipt,
            'taxAmountsByRate' => $this->taxAmountsByRate,
        ];
    }
} 