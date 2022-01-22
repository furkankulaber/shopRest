<?php

namespace App\Service;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ShoppingService
{
    private ContainerInterface $container;
    private UserRepository $userRepository;
    private AddressRepository $addressRepository;
    private OrderRepository $orderRepository;

    const STATUS_INITIALIZE = 'i';
    const STATUS_SHIPPED = 's';
    const STATUS_DELETED = 'd';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine')->getManager();
        $this->userRepository = $em->getRepository(User::class);
        $this->addressRepository = $em->getRepository(Address::class);
        $this->orderRepository = $em->getRepository(Order::class);
        $this->orderRepository = $em->getRepository(Order::class);
    }

    /**
     * @throws \Exception
     */
    public function addOrder($data, User $user)
    {
        $addressResponse = $this->addressRepository->findOneWithByResponse(['id' => $data->address, 'user' => $user]);
        if (!$addressResponse->isSuccess() && $addressResponse->getException()) {
            return new ServiceResponse($addressResponse->getException(), false);
        } elseif (!$addressResponse->getResponse() instanceof Address) {
            return new ServiceResponse(null, false, 'Gönderilen değerler eksik veya yanlış');
        }

        if (count($data->product) < 1) return new ServiceResponse(null, false, 'Gönderilen değerler eksik veya yanlış');

        $orderItems = [];
        foreach ($data->product as $product) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($product->id)->setQuantity($product->qty)->setTitle($product->title);
            $orderItems[] = $orderItem;
        }

        $date = new \DateTime($data->shippingDate);

        $insertData = [
            'user' => $user,
            'address' => $addressResponse->getResponse(),
            'orderCode' => $this->createOrderNumder($user),
            'orderItem' => $orderItems,
            'status' => 'i',
            'shippingAt' => $date,
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime()
        ];

        $insertResponse = $this->orderRepository->insert($insertData);
        if ($insertResponse->getException()) {
            return new ServiceResponse($insertResponse->getException(), false, 'Bir problem oluştu');
        }
        return new ServiceResponse($insertResponse->getResponse(), true, 'İşleminiz başarıyla gerçekleştirilmiştir.');
    }

    public function updateOrder($data, Order $order)
    {
        $shippingDate = new \DateTime($data->shippingAt ?? null);
        $status = $data->status ?? null;
        $nowDate = new \DateTime('now');
        $updateData = [];
        if($status){
            if (!in_array($status, [self::STATUS_DELETED, self::STATUS_INITIALIZE, self::STATUS_SHIPPED])) {
                return new ServiceResponse(null, false, 'Gönderilen değer hatalı');
            }
            $updateData['status'] = $data->status;
        }
        if ($shippingDate) {
            $updateData['shippingAt'] = $shippingDate;
            if ($shippingDate->getTimestamp() <= $nowDate->getTimestamp()) {
                $updateData['status'] = 's';
            }
        }
        if (count($updateData) < 1) {
            return new ServiceResponse(null, false, 'Gönderilen değer hatalı');
        }
        $updateResponse = $this->orderRepository->update($order, $updateData);
        if ($updateResponse->getException()) {
            return new ServiceResponse($updateResponse->getException(), false, 'Bir problem oluştu');
        }
        return new ServiceResponse($updateResponse->getResponse(), true, 'İşleminiz başarıyla gerçekleştirilmiştir.');
    }

    private function createOrderNumder(User $user)
    {
        return $user->getId() . '.' . date('YmdHis');
    }


    public function updateOrderStatus($status, Order $order)
    {
        if (!in_array($status, [self::STATUS_DELETED, self::STATUS_INITIALIZE, self::STATUS_SHIPPED])) {
            return new ServiceResponse(null, false, 'Gönderilen değer hatalı');
        }

        $updateData = ['status' => $status];
        if ($status === 's') {
            $updateData['shippingAt'] = new \DateTime();
        }

        $updateResponse = $this->orderRepository->update($order, $updateData);

        if ($updateResponse->getException()) {
            return new ServiceResponse($updateResponse->getException(), false, 'Bir problem oluştu');
        }
        return new ServiceResponse($updateResponse->getResponse(), true, 'İşleminiz başarıyla gerçekleştirilmiştir.');
    }

    public function updateOrderAddress(Address $address, Order $order)
    {
        $updateData = ['address' => $address];

        $updateResponse = $this->orderRepository->update($order, $updateData);

        if ($updateResponse->getException()) {
            return new ServiceResponse($updateResponse->getException(), false, 'Bir problem oluştu');
        }
        return new ServiceResponse($updateResponse->getResponse(), true, 'İşleminiz başarıyla gerçekleştirilmiştir.');
    }

}
