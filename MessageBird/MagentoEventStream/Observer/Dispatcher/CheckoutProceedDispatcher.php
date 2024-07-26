<?php

namespace MessageBird\MagentoEventStream\Observer\Dispatcher;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use MessageBird\MagentoEventStream\Model\StringStorage;

class CheckoutProceedDispatcher implements ObserverInterface
{
    protected $curl;
    protected $session;
    protected $stringStorage;


    public function __construct(
        Curl          $curl,
        Session       $session,
        StringStorage $stringStorage
    )
    {
        $this->curl = $curl;
        $this->session = $session;
        $this->stringStorage = $stringStorage;
    }

    public function execute(Observer $observer)
    {
        $quote = ObjectManager::getInstance()->get('\Magento\Checkout\Model\Cart')->getQuote();

        $productsList = [];
        foreach ($quote->getAllItems() as $item) {
            $obj = [
                'id' => $item->getProduct()->getId(),
                'name' => $item->getProduct()->getName(),
                'price' => $item->getProduct()->getFinalPrice(),
                'sku' => $item->getProduct()->getSku(),
                'qty' => $item->getQty(),
            ];
            $productsList[] = $obj;
        }

        $payload = [
            'quote' => $quote->debug(),
            'products' => $productsList,
            'checkout_id' => $this->session->getSessionId(),
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
