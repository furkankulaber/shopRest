<?php

namespace App\Controller;

use App\Entity\Address;
use App\Service\ResponseService\Constants;
use App\Service\ShoppingService;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\ViewObjects\Order as OrderView;

/**
 * @package App\Controller
 * @Route("/api/shopping", name="shopping")
 */
class ShoppingController extends CoreController
{

    /**
     * @Route("/insert", name="_insert", methods={"POST"})
     */
    public function createOrder()
    {
        $sService = new ShoppingService($this->getContainer());
        $data = json_decode($this->getRequest()->getContent());
        $insertResponse = $sService->addOrder($data, $this->getUser());
        if (!$insertResponse->isSuccess() && $insertResponse->getException()) {
            return $this->getResponseService()->toJsonResponse($insertResponse->getException(), Constants::MSG_500_0000);
        } elseif (!$insertResponse->isSuccess()) {
            return $this->getResponseService()->toJsonResponse(null, Constants::MSG_200_0401, $insertResponse->getMessage());
        }

        return $this->getResponseService()->toJsonResponse(OrderView::create($insertResponse->getResponse())->toArray(), Constants::MSG_200_0000, $insertResponse->getMessage());
    }

    /**
     * @Route("/{orderId}", name="_detail", methods={"GET"})
     */
    public function detailOrder($orderId)
    {
        $orderRepo = $this->getEntityManager()->getRepository(Order::class);
        $orderResponse = $orderRepo->findOneWithByResponse(['id' => $orderId, 'user' => $this->getUser()]);

        if ($orderResponse->getException()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        if (!$orderResponse->getResponse() instanceof Order) {
            return $this->getResponseService()->toJsonResponse(null, Constants::MSG_200_0404, 'Sipariş bulunamadı');
        }

        return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0000, $orderResponse->getMessage());
    }

    /**
     * @Route("", name="_list", methods={"GET"})
     */
    public function listOrder()
    {
        $orderRepo = $this->getEntityManager()->getRepository(Order::class);
        $orderResponse = $orderRepo->findByWithResponse(['user' => $this->getUser()]);

        if ($orderResponse->getException()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }
        $orderList = [];
        foreach ($orderResponse->getResponse() as $order) {
            $orderList[] = OrderView::create($order)->toArray();
        }

        return $this->getResponseService()->toJsonResponse($orderList, Constants::MSG_200_0000, $orderResponse->getMessage());
    }

    /**
     * @param $orderId
     * @return JsonResponse
     * @Route("/{orderId}", name="_update", methods={"PUT"})
     */
    public function editOrder($orderId)
    {
        $sService = new ShoppingService($this->getContainer());
        $orderRepo = $this->getEntityManager()->getRepository(Order::class);
        $data = json_decode($this->getRequest()->getContent());
        $orderResponse = $orderRepo->findOneWithByResponse(['id' => $orderId, 'user' => $this->getUser()]);

        if ($orderResponse->getException()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        if (!$orderResponse->getResponse() instanceof Order) {
            return $this->getResponseService()->toJsonResponse(null, Constants::MSG_200_0404, 'Sipariş bulunamadı');
        }

        if($orderResponse->getResponse()->getStatus() == 's'){
            return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0401, 'Sipariş kargolandığı için durumu değiştirilemez');
        }

        $orderResponse = $sService->updateOrder($data, $orderResponse->getResponse());
        if (!$orderResponse->isSuccess()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0000, $orderResponse->getMessage());
    }

    /**
     * @param $status
     * @param $orderId
     * @return JsonResponse
     * @Route("/status/{orderId}/{status}", name="_update_status", methods={"PATCH"})
     */
    public function updateStatus($orderId, $status)
    {
        $sService = new ShoppingService($this->getContainer());
        $orderRepo = $this->getEntityManager()->getRepository(Order::class);
        $orderResponse = $orderRepo->findOneWithByResponse(['id' => $orderId, 'user' => $this->getUser()]);

        if ($orderResponse->getException()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        if (!$orderResponse->getResponse() instanceof Order) {
            return $this->getResponseService()->toJsonResponse(null, Constants::MSG_200_0404, 'Sipariş bulunamadı');
        }

        if($orderResponse->getResponse()->getStatus() == 's'){
            return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0401, 'Sipariş kargolandığı için durumu değiştirilemez');
        }

        $orderResponse = $sService->updateOrderStatus($status, $orderResponse->getResponse());
        if (!$orderResponse->isSuccess()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0000, $orderResponse->getMessage());
    }

    /**
     * @param $address
     * @param $orderId
     * @return JsonResponse
     * @Route("/address/{orderId}/{address}", name="_update_address", methods={"PATCH"})
     */
    public function updateAddress($orderId, $address)
    {
        $sService = new ShoppingService($this->getContainer());
        $orderRepo = $this->getEntityManager()->getRepository(Order::class);
        $addressRepo = $this->getEntityManager()->getRepository(Address::class);
        $orderResponse = $orderRepo->findOneWithByResponse(['id' => $orderId, 'user' => $this->getUser()]);
        $addressResponse = $addressRepo->findOneWithByResponse(['id' => $address, 'user' => $this->getUser()]);

        if ($orderResponse->getException() || $addressResponse->getException()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        if (!$orderResponse->getResponse() instanceof Order || !$addressResponse->getResponse() instanceof Address) {
            return $this->getResponseService()->toJsonResponse(null, Constants::MSG_200_0404, 'Sipariş bulunamadı');
        }

        if($orderResponse->getResponse()->getStatus() == 's'){
            return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0401, 'Sipariş kargolandığı için durumu değiştirilemez');
        }

        $orderResponse = $sService->updateOrderAddress($addressResponse->getResponse(), $orderResponse->getResponse());
        if (!$orderResponse->isSuccess()) {
            return $this->getResponseService()->toJsonResponse($orderResponse->getException(), Constants::MSG_500_0000, $orderResponse->getMessage());
        }

        return $this->getResponseService()->toJsonResponse(OrderView::create($orderResponse->getResponse())->toArray(), Constants::MSG_200_0000, $orderResponse->getMessage());
    }

}
