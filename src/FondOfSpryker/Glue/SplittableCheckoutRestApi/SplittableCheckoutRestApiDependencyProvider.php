<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToCheckoutRestApiClientBridge;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig getConfig()
 */
class SplittableCheckoutRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_CHECKOUT_REST_API = 'CLIENT_CHECKOUT_REST_API';
    public const PLUGINS_CHECKOUT_REQUEST_ATTRIBUTES_VALIDATOR = 'PLUGINS_CHECKOUT_REQUEST_ATTRIBUTES_VALIDATOR';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCheckoutRestApiClient($container);
        $container = $this->addCheckoutRequestAttributesValidatorPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCheckoutRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_CHECKOUT_REST_API] = static function (Container $container) {
            return new SplittableCheckoutRestApiToCheckoutRestApiClientBridge(
                $container->getLocator()->checkoutRestApi()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCheckoutRequestAttributesValidatorPlugins(Container $container): Container
    {
        $container[static::PLUGINS_CHECKOUT_REQUEST_ATTRIBUTES_VALIDATOR] = function () {
            return $this->getCheckoutRequestAttributesValidatorPlugins();
        };

        return $container;
    }

    /**
     * @return \Spryker\Glue\CheckoutRestApiExtension\Dependency\Plugin\CheckoutRequestAttributesValidatorPluginInterface[]
     */
    protected function getCheckoutRequestAttributesValidatorPlugins(): array
    {
        return [];
    }
}
