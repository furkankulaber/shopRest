<?php

namespace App\ViewObjects;

use App\Entity\Order as OrderEntity;
use App\Entity\OrderItem;

class Order extends BaseViewObject
{

    private OrderEntity $order;

    public function __construct(OrderEntity $data)
    {
        $this->order = $data;
    }

    public static function create($data)
    {
        return new self($data);
    }

    private function items($data)
    {
        $items = [];
        /** @var OrderItem $product */
        foreach ($data as $product)
        {
            $items[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'qty' => $product->getQuantity()
            ];
        }
        return $items;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->order->getId(),
            'orderCode' => $this->order->getOrderCode(),
            'user' => User::create($this->order->getUser())->toArray(),
            'address' => Address::create($this->order->getAddress())->toArray(),
            'items' => $this->items($this->order->getOrderItems()),
            'status' => $this->order->getStatus(),
            'shippingAt' => $this->order->getShippingAt()->format(DATE_RFC3339),
            'createdAt' => $this->order->getCreatedAt()->format(DATE_RFC3339),
            'updatedAt' => $this->order->getUpdatedAt()->format(DATE_RFC3339),
        ];
    }
}
