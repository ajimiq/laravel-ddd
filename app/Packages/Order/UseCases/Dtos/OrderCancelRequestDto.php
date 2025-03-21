<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 注文キャンセルリクエストDTO
 */
class OrderCancelRequestDto
{
    /**
     * @param string $orderId 注文ID
     * @param string $cancelReason キャンセル理由
     * @param string $canceledAt キャンセル日時
     */
    public function __construct(
        private readonly string $orderId,
        private readonly string $cancelReason,
        private readonly string $canceledAt
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

    /**
     * キャンセル理由を取得
     *
     * @return string
     */
    public function getCancelReason(): string
    {
        return $this->cancelReason;
    }

    /**
     * キャンセル日時を取得
     *
     * @return string
     */
    public function getCanceledAt(): string
    {
        return $this->canceledAt;
    }
}
