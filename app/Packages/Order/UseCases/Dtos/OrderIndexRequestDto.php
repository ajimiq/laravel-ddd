<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 注文一覧の検索条件DTO
 */
class OrderIndexRequestDto
{
    /**
     * @param string|null $status 注文ステータス
     * @param string|null $orderedFrom 注文日（開始）
     * @param string|null $orderedTo 注文日（終了）
     * @param int $perPage ページあたりの表示件数
     */
    public function __construct(
        private readonly ?string $status = null,
        private readonly ?string $orderedFrom = null,
        private readonly ?string $orderedTo = null,
        private readonly int $perPage = 10
    ) {
    }

    /**
     * 検索条件の配列を取得
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'ordered_from' => $this->orderedFrom,
            'ordered_to' => $this->orderedTo,
        ];
    }

    /**
     * ステータスを取得
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * 注文日（開始）を取得
     *
     * @return string|null
     */
    public function getOrderedFrom(): ?string
    {
        return $this->orderedFrom;
    }

    /**
     * 注文日（終了）を取得
     *
     * @return string|null
     */
    public function getOrderedTo(): ?string
    {
        return $this->orderedTo;
    }

    /**
     * ページあたりの表示件数を取得
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }
} 