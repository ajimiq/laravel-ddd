<?php

namespace App\Packages\Order\UseCases;

use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use App\Packages\Order\UseCases\Dtos\OrderCancelRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderCancelResponseDto;
use Exception;
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
                $orderId = new OrderId($requestDto->getOrderId());
                $order = $this->orderRepository->find($orderId);

                if (!$order) {
                    throw new Exception('注文が見つかりませんでした。');
                }

                // キャンセル可能かチェック
                if ($order->getStatus()->getValue() !== 'unshipped') {
                    throw new \RuntimeException('この注文はキャンセルできません。');
                }

                // 注文をキャンセル
                $order->cancel($requestDto->getCancelReason());
                $this->orderRepository->cancel($order);
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
