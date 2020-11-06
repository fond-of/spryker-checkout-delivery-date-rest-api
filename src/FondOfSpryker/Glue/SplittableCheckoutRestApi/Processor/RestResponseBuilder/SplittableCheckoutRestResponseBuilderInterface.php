<?php


namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder;

use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface SplittableCheckoutRestResponseBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createSplittableCheckoutRestResponse(
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestResponseInterface;
}
