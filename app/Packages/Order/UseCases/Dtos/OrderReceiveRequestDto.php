<?php

namespace App\Packages\Order\UseCases\Dtos;

/**
 * 注文取得リクエストDTO
 */
class OrderReceiveRequestDto
{
    /**
     * @param int $fromDays 取得開始日（n日前）
     * @param int $toDays 取得終了日（n日前）
     * @param int $limit 取得件数
     */
    public function __construct(
        private readonly int $fromDays = 30,
        private readonly int $toDays = 0,
        private readonly int $limit = 10
    ) {
    }

    /**
     * 取得開始日（n日前）を取得
     *
     * @return int
     */
    public function getFromDays(): int
    {
        return $this->fromDays;
    }

    /**
     * 取得終了日（n日前）を取得
     *
     * @return int
     */
    public function getToDays(): int
    {
        return $this->toDays;
    }

    /**
     * 取得件数を取得
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
