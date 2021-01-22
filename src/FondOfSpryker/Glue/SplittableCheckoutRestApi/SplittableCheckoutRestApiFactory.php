<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToGlossaryStorageClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Mapper\RestSplittableCheckoutErrorMapper;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Mapper\RestSplittableCheckoutErrorMapperInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapper;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapperInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpander;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder\SplittableCheckoutRestResponseBuilder;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder\SplittableCheckoutRestResponseBuilderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\SplittableCheckoutProcessor;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\SplittableCheckoutProcessorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidator;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface;
use Spryker\Glue\Kernel\AbstractFactory;

/**
 * @method \FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface getClient()
 * @method \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig getConfig()
 */
class SplittableCheckoutRestApiFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\SplittableCheckoutProcessorInterface
     */
    public function createSplittableCheckoutProcessor(): SplittableCheckoutProcessorInterface
    {
        return new SplittableCheckoutProcessor(
            $this->getClient(),
            $this->createCheckoutRequestValidator(),
            $this->createSplittableCheckoutRequestAttributesExpander(),
            $this->createSplittableCheckoutRestResponseBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface
     */
    public function createCheckoutRequestValidator(): SplittableCheckoutRequestValidatorInterface
    {
        return new SplittableCheckoutRequestValidator(
            $this->getSplittableCheckoutRequestValidatorPlugins(),
            $this->getSplittableCheckoutRequestAttributesValidatorPlugins()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface
     */
    public function createSplittableCheckoutRequestAttributesExpander(): SplittableCheckoutRequestAttributesExpanderInterface
    {
        return new SplittableCheckoutRequestAttributesExpander(
            $this->createCustomerMapper(),
            $this->getConfig(),
            $this->getSplittableCheckoutRequestExpanderPlugins()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder\SplittableCheckoutRestResponseBuilderInterface
     */
    protected function createSplittableCheckoutRestResponseBuilder(): SplittableCheckoutRestResponseBuilderInterface
    {
        return new SplittableCheckoutRestResponseBuilder(
            $this->getResourceBuilder(),
            $this->createRestSplittableCheckoutErrorMapper()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapperInterface
     */
    public function createCustomerMapper(): CustomerMapperInterface
    {
        return new CustomerMapper();
    }

    public function createRestSplittableCheckoutErrorMapper(): RestSplittableCheckoutErrorMapperInterface
    {
        return new RestSplittableCheckoutErrorMapper(
            $this->getConfig(),
            $this->getGlossaryStorageClient()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplttableCheckoutRequestValidatorPluginInterface[]
     */
    public function getSplittableCheckoutRequestValidatorPlugins(): array
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::PLUGINS_SPLITTABLE_CHECKOUT_REQUEST_VALIDATOR);
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplttableCheckoutRequestAttributesValidatorPluginInterface[]
     */
    public function getSplittableCheckoutRequestAttributesValidatorPlugins(): array
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::PLUGINS_SPLITTABLE_CHECKOUT_REQUEST_ATTRIBUTES_VALIDATOR);
    }

    /**
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutRequestExpanderPluginInterface[]
     */
    public function getSplittableCheckoutRequestExpanderPlugins(): array
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::PLUGINS_SPLITTABLE_CHECKOUT_REQUEST_EXPANDER);
    }

    /**
     * @return \Spryker\Glue\CheckoutRestApi\Dependency\Client\CheckoutRestApiToGlossaryStorageClientInterface
     */
    public function getGlossaryStorageClient(): SplittableCheckoutRestApiToGlossaryStorageClientInterface
    {
        return $this->getProvidedDependency(SplittableCheckoutRestApiDependencyProvider::CLIENT_GLOSSARY_STORAGE);
    }
}
