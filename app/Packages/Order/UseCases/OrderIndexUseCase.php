<?php

namespace App\Packages\Order\UseCases;

use App\Models\Order as OrderModel;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class OrderIndexUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * 注文一覧を取得
     * 
     * @param array{
     *   status?: string,
     *   ordered_from?: string,
     *   ordered_to?: string
     * } $searchParams
     * @param int $perPage
     * @return array{
     *   orders: LengthAwarePaginator,
     *   statuses: array<string, string>,
     *   search: array{
     *     status: ?string,
     *     ordered_from: ?string,
     *     ordered_to: ?string
     *   }
     * }
     */
    public function execute(array $searchParams = [], int $perPage = 10): array
    {
        Log::channel('online')->info(var_export($searchParams, true));

        // リポジトリを使用して注文一覧を取得
        $orders = $this->orderRepository->searchOrders($searchParams, $perPage);

        // ステータス一覧
        $statuses = [
            'pending' => '保留中',
            'cancelled' => 'キャンセル',
            'unshipped' => '未発送',
        ];

        return [
            'orders' => $orders,
            'statuses' => $statuses,
            'search' => [
                'status' => $searchParams['status'] ?? null,
                'ordered_from' => $searchParams['ordered_from'] ?? null,
                'ordered_to' => $searchParams['ordered_to'] ?? null,
            ],
        ];
    }
} 