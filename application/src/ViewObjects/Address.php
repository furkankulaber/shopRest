<?php

namespace App\ViewObjects;

use App\Entity\Address as AddressEntity;
use App\ViewObjects\BaseViewObject;

class Address extends BaseViewObject
{

    private AddressEntity $address;

    public function __construct(AddressEntity $address)
    {
        $this->address = $address;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->address->getId(),
            'title' => $this->address->getTitle(),
            'address' => $this->address->getAddress(),
            'createdAt' => $this->address->getCreatedAt()->format(DATE_RFC3339),
            'updatedAt' => $this->address->getUpdatedAt()->format(DATE_RFC3339),
        ];
    }

    public static function create($data)
    {
        return new self($data);
    }
}
