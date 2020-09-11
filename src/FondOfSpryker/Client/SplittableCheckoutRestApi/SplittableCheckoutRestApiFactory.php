<?php

namespace FondOfSpryker\Client\SplittableCheckoutRestApi;

use FondOfSpryker\Client\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToZedRequestClientInterface;
use FondOfSpryker\Client\SplittableCheckoutRestApi\Zed\SplittableCheckoutRestApiZedStub;
use FondOfSpryker\Client\SplittableCheckoutRestApi\Zed\SplittableCheckoutRestApiZedStubInterface;
use Spryker\Client\Kernel\AbstractFactory;

class SplittableCheckoutRestApiFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Client\SplittableCheckoutRestApi\Zed\SplittableCheckoutRestApiZedStubInterface
     */
    public function createSplittableCheckoutRestApiZedStub(): SplittableCheckoutRestApiZedStubInterface
    {
        return new SplittableCheckoutRestApiZedStub($this->getZedRequestClient());
    }

    /**
     * @return \FondOfSpryker\Client\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToZedRequestClientInterface
     *
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getZedRequestClient(): SplittableCheckoutRestApiToZedRequestClientInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
