<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 領収書表示結果DTO
 */
class OrderShowReceiptResponseDto
{
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
     * @param array<float|string, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }> $taxAmountsByRate 税率ごとの金額
     */
    public function __construct(
        private readonly array $order,
        private readonly array $company,
        private readonly array $receipt,
        private readonly array $taxAmountsByRate
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
     * ビューに渡すデータを取得
     *
     * @return array<string, mixed>
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
