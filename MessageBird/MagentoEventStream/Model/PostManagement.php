<?php

namespace MessageBird\MagentoEventStream\Model;

use MessageBird\MagentoEventStream\Model\StringStorage;
use Magento\Framework\Webapi\Rest\Request;
use MessageBird\MagentoEventStream\Api\PostManagementInterface;

class PostManagement implements PostManagementInterface
{
    private $request;
    private $stringStorage;

    public function __construct(
        StringStorage $stringStorage,
        Request       $request
    )
    {
        $this->request = $request;
        $this->stringStorage = $stringStorage;
    }

    /**
     * @return string
     */
    public function createConnection(): string
    {
        $body = $this->request->getBodyParams();
        if (empty($body)) {
            return 'Invalid body.';
        }

        $url = $body['webhookUrl'];
        if (empty($url)) {
            return 'Invalid value provided.';
        }

        $this->stringStorage->addCallbackUrl($url);
        return "Connection established!";
    }

    /**
     * @return string
     */
    public function teardownConnection(): string
    {
        if (empty($this->stringStorage->getAllCallbackUrls())) {
            return 'No connections to terminate';
        }

        $body = $this->request->getBodyParams();
        if (empty($body)) {
            return 'Invalid body.';
        }

        $url = $body['webhookUrl'];
        if (empty($url)) {
            return 'Invalid value provided.';
        }

        $this->stringStorage->removeCallbackUrl($url);
        return 'Connection closed!';
    }

    /**
     * @return array
     */
    public function fetchConnections(): array
    {
        return $this->stringStorage->getAllCallbackUrls();
    }
}
