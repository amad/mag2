<?php

namespace MessageBird\MagentoEventStream\Api;

interface PostManagementInterface
{
    /**
     * @return string
     */
    public function createConnection(): string;

    /**
     * @return string
     */
    public function teardownConnection(): string;

    /**
     * @return array
     */
    public function fetchConnections(): array;
}
