<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 注文キャンセル結果DTO
 */
class OrderCancelResponseDto
{
    /**
     * @param bool $success 成功したかどうか
     * @param string $message メッセージ
     * @param string|null $orderId 注文ID
     */
    public function __construct(
        private readonly bool $success,
        private readonly string $message,
        private readonly ?string $orderId = null
    ) {
    }

    /**
     * 成功したかどうかを取得
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * メッセージを取得
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * 注文IDを取得
     *
     * @return string|null
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * 配列に変換
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'order_id' => $this->orderId,
        ];
    }
}
