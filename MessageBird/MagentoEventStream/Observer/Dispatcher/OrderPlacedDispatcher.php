<?php

namespace MessageBird\MagentoEventStream\Observer\Dispatcher;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Sales\Api\OrderRepositoryInterface;
use MessageBird\MagentoEventStream\Model\StringStorage;

class OrderPlacedDispatcher implements ObserverInterface
{
    protected $curl;
    protected $orderRepository;
    protected $stringStorage;

    public function __construct(
        Curl                     $curl,
        OrderRepositoryInterface $orderRepository,
        StringStorage            $stringStorage
    )
    {
        $this->curl = $curl;
        $this->orderRepository = $orderRepository;
        $this->stringStorage = $stringStorage;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $productsList = [];
        foreach ($order->getAllItems() as $item) {
            $obj = [
                'id' => $item->getProduct()->getId(),
                'name' => $item->getProduct()->getName(),
                'price' => $item->getProduct()->getFinalPrice(),
                'sku' => $item->getProduct()->getSku(),
                'qty' => $item->getQtyOrdered(),
            ];
            $productsList[] = $obj;
        }

        $payload = [
            'order' => $order->debug(),
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
