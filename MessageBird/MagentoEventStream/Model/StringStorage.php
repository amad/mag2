<?php

namespace MessageBird\MagentoEventStream\Model;

use Magento\Framework\App\CacheInterface;

class StringStorage
{
    const CACHE_KEY = 'messagebird_callback_urls'; // Unique cache key

    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function addCallbackUrl($url)
    {
        $callbackUrls = $this->retrieveCallbackUrls();
        $callbackUrls[] = $url;
        $this->storeCallbackUrls($callbackUrls);
    }

    public function removeCallbackUrl($url)
    {
        $callbackUrls = $this->retrieveCallbackUrls();
        $index = array_search($url, $callbackUrls);
        if ($index !== false) {
            unset($callbackUrls[$index]);
            $this->storeCallbackUrls($callbackUrls);
        }
    }

    public function getAllCallbackUrls()
    {
        return $this->retrieveCallbackUrls();
    }

    private function retrieveCallbackUrls()
    {
        $cachedData = $this->cache->load(self::CACHE_KEY);
        return $cachedData ? json_decode($cachedData, true) : [];
    }

    private function storeCallbackUrls($callbackUrls)
    {
        $encodedData = json_encode($callbackUrls);
        $this->cache->save($encodedData, self::CACHE_KEY);
    }
}
