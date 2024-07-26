<?php

namespace MessageBird\MagentoEventStream\Observer\Dispatcher;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Sales\Api\OrderRepositoryInterface;
use MessageBird\MagentoEventStream\Model\StringStorage;

class ShipmentFulfilledDispatcher implements ObserverInterface
{
    protected $curl;
    protected $customerRepository;
    protected $orderRepository;
    protected $stringStorage;

    public function __construct(
        Curl                        $curl,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface    $orderRepository,
        StringStorage               $stringStorage
    )
    {
        $this->curl = $curl;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->stringStorage = $stringStorage;
    }

    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        $productsList = [];
        foreach ($shipment->getAllItems() as $item) {
            $obj = [
                'id' => $item->getProductId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'sku' => $item->getSku(),
                'qty' => $item->getQty(),
            ];
            $productsList[] = $obj;
        }

        $payload = [
            'shipment' => $shipment->debug(),
            'order' => $this->orderRepository->get($shipment->getOrderId())->debug(),
            'products' => $productsList,
            'event_name' => $observer->getEvent()->getName(),
            'created_at' => round(microtime(true) * 1000),
        ];
        $jsonPayload = json_encode($payload);

        foreach ($this->stringStorage->getAllCallbackUrls() as $value) {
            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->post($value, $jsonPayload);
        }
        return $this;
    }
}
