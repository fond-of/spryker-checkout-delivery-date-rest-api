<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class SplittableCheckoutRestResponseBuilder implements SplittableCheckoutRestResponseBuilderInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder
    ) {
        $this->restResourceBuilder = $restResourceBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createSplittableCheckoutRestResponse(
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestResponseInterface {

        $restResponse = $this->restResourceBuilder->createRestResponse();

        return $restResponse->addResource(
            $this->createSplittableCheckoutResource($restSplittableCheckoutResponseTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    public function createSplittableCheckoutResource(
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestResourceInterface {

        return $this->restResourceBuilder->createRestResource(
            SplittableCheckoutRestApiConfig::RESOURCE_SPLITTABLE_CHECKOUT,
            null,
            $restSplittableCheckoutResponseTransfer
        );
    }
}
