<?php

namespace App\Packages\Order\UseCases;

use App\Models\Order;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\UseCases\Dtos\OrderCancelRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderCancelResponseDto;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * 注文キャンセルユースケース
 */
class OrderCancelUseCase
{
    /**
     * コンストラクタ
     * 
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * 注文をキャンセル
     * 
     * @param OrderCancelRequestDto $requestDto
     * @return OrderCancelResponseDto
     */
    public function execute(OrderCancelRequestDto $requestDto): OrderCancelResponseDto
    {
        DB::transaction(function () use ($requestDto) {
            try {

                // 注文を取得
                $order = Order::lockForUpdate()->findOrFail($requestDto->getOrderId());

                // キャンセル可能かチェック
                if ($order->status !== 'unshipped') {
                    throw new \RuntimeException('この注文はキャンセルできません。');
                }

                // 注文をキャンセル
                $order->update([
                    'status' => 'cancelled',
                    'canceled_at' => now(),
                    'cancel_reason' => $requestDto->getCancelReason(),
                ]);
            } catch (Exception $e) {
                // エラーレスポンスを返す
                return new OrderCancelResponseDto(
                    false,
                    sprintf('注文キャンセル処理中にエラーが発生しました: %s', $e->getMessage()),
                );
            }
        });
        // 成功レスポンスを返す
        return new OrderCancelResponseDto(
            true,
            sprintf('注文をキャンセルしました: %s', $requestDto->getOrderId()),
            $requestDto->getOrderId()
        );
    }
} 