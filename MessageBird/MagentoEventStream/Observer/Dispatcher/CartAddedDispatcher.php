<?php

namespace MessageBird\MagentoEventStream\Observer\Dispatcher;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use MessageBird\MagentoEventStream\Model\StringStorage;

class CartAddedDispatcher implements ObserverInterface
{
    protected $curl;
    protected $stringStorage;

    public function __construct(
        Curl          $curl,
        StringStorage $stringStorage
    )
    {
        $this->curl = $curl;
        $this->stringStorage = $stringStorage;
    }

    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();

        $payload = [
            'quote' => $event->debug(),
            'event_name' => $event->getName(),
            'created_at' => round(microtime(true) * 1000),
        ];
        $jsonPayload = json_encode($payload);

        foreach ($this->stringStorage->getAllCallbackUrls() as $value)
        {
            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->post($value, $jsonPayload);
        }
        return $this;
    }
}
