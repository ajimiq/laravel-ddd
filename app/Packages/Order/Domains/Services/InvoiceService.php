<?php

namespace App\Packages\Order\Domains\Services;

use App\Packages\Order\Domains\ValueObjects\Order;

use Illuminate\Support\Facades\Log;

class InvoiceService
{
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
    public function getTaxAmountsByRate(Order $order): array
    {
        // 商品の税額を税率ごとに集計
        $itemTaxes = [];
        foreach ($order->getOrderItems() as $item) {
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
        $shippingFee = $order->getShippingFee();
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
