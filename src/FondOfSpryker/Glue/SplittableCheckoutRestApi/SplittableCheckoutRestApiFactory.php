<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi;

use FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessor;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Checkout\CheckoutProcessorInterface;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiError;
use FondOfSpryker\Glue\CheckoutRestApi\Processor\Validation\RestApiErrorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToCheckoutRestApiClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapper;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapperInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpander;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\SplittableCheckoutProcessor;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\SplittableCheckoutProcessorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\CheckoutRequestValidator;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidator;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\Kernel\AbstractFactory;

/**
 * @method \FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface getClient()
 * @method \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig getConfig()
 */
class SplittableCheckoutRestApiFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\SplittableCheckoutProcessorInterface
     *
     * @throws \Spryker\Glue\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createSplittableCheckoutProcessor(): SplittableCheckoutProcessorInterface
    {
        return new SplittableCheckoutProcessor(
            $this->getClient(),
            $this->createCheckoutRequestValidator(),
            $this->createSplittableCheckoutRequestAttributesExpander(),
            $this->getResourceBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface
     */
    public function createCheckoutRequestValidator(): SplittableCheckoutRequestValidatorInterface
    {
        return new SplittableCheckoutRequestValidator(
            $this->getCheckoutRequestAttributesValidatorPlugins()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface
     */
    public function createSplittableCheckoutRequestAttributesExpander(): SplittableCheckoutRequestAttributesExpanderInterface
    {
        return new SplittableCheckoutRequestAttributesExpander(
            $this->createCustomerMapper(),
            $this->getConfig()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapperInterface
     */
    public function createCustomerMapper(): CustomerMapperInterface
    {
        return new CustomerMapper();
    }

    /**
     * @return \Spryker\Glue\CheckoutRestApiExtension\Dependency\Plugin\CheckoutRequestAttributesValidatorPluginInterface[]
     */
    public function getCheckoutRequestAttributesValidatorPlugins(): array
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::PLUGINS_CHECKOUT_REQUEST_ATTRIBUTES_VALIDATOR);
    }
}
