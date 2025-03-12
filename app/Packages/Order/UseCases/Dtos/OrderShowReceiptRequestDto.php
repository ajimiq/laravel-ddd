<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 領収書表示リクエストDTO
 */
class OrderShowReceiptRequestDto
{
    /**
     * @param string $orderId 注文ID
     */
    public function __construct(
        private readonly string $orderId
    ) {
    }

    /**
     * 注文IDを取得
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }
} 