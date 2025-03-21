<?php

namespace App\Packages\Order\Domains\ValueObjects;

class OrderCustomerInfo
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $phoneNumber,
        private readonly string $address
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
        ];
    }
}
