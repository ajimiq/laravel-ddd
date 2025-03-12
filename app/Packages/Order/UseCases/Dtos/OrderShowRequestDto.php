<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 注文詳細の取得リクエストDTO
 */
class OrderShowRequestDto
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