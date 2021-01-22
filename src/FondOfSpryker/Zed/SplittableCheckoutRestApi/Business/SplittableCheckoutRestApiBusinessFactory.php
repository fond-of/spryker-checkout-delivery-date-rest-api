<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Business;

use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Address\AddressReader;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Address\AddressReaderInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\PlaceOrderProcessor;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\PlaceOrderProcessorInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReader;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator\SplittableCheckoutValidator;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator\SplittableCheckoutValidatorInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCalculationFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartsRestApiFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCheckoutFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToPaymentFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToQuoteFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToShipmentFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\SplittableCheckoutRestApiDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

class SplittableCheckoutRestApiBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\PlaceOrderProcessorInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPlaceOrderProcessor(): PlaceOrderProcessorInterface
    {
        return new PlaceOrderProcessor(
            $this->getSplittableCheckoutFacade(),
            $this->getCalculationFacade(),
            $this->createSplittableCheckoutValidator(),
            $this->getQuoteMapperPlugins()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator\SplittableCheckoutValidatorInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createSplittableCheckoutValidator(): SplittableCheckoutValidatorInterface
    {
        return new SplittableCheckoutValidator(
            $this->createQuoteReader(),
            $this->getSplittableCheckoutDataValidatorPlugins()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createQuoteReader(): QuoteReaderInterface
    {
        return new QuoteReader($this->getCartsRestApiFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Address\AddressReaderInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAddressReader(): AddressReaderInterface
    {
        return new AddressReader($this->getCustomerFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCartFacade(): SplittableCheckoutRestApiToCartFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_CART);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartsRestApiFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCartsRestApiFacade(): SplittableCheckoutRestApiToCartsRestApiFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_CARTS_REST_API);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCheckoutFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getSplittableCheckoutFacade(): SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_SPLITTABLE_CHECKOUT);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckoutRestApiToCustomerFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCustomerFacade(): SplittableCheckoutRestApiToCustomerFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToPaymentFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPaymentFacade(): SplittableCheckoutRestApiToPaymentFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_PAYMENT);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToQuoteFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getQuoteFacade(): SplittableCheckoutRestApiToQuoteFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_QUOTE);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToShipmentFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getShipmentFacade(): SplittableCheckoutRestApiToShipmentFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_SHIPMENT);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCalculationFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCalculationFacade(): SplittableCheckoutRestApiToCalculationFacadeInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::FACADE_CALCULATION);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[]
     */
    public function getQuoteMapperPlugins(): array
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::PLUGINS_QUOTE_MAPPER);
    }

    /**
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutDataValidatorPluginInterface[]
     */
    public function getSplittableCheckoutDataValidatorPlugins(): array
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::PLUGINS_SPLITTABLE_CHECKOUT_DATA_VALIDATOR);
    }
}
