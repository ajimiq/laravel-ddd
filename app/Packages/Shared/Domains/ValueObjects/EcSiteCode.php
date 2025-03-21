<?php

namespace App\Packages\Shared\Domains\ValueObjects;

class EcSiteCode
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new \InvalidArgumentException('ECサイトコードは必須です。');
        }
        if (strlen($value) > 20) {
            throw new \InvalidArgumentException('ECサイトコードは20文字以内で指定してください。');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
