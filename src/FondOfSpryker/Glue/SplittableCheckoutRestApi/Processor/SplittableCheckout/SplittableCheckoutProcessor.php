<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout;

use FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder\SplittableCheckoutRestResponseBuilderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\Kernel\PermissionAwareTrait;

class SplittableCheckoutProcessor implements SplittableCheckoutProcessorInterface
{
    use PermissionAwareTrait;

    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder\SplittableCheckoutRestResponseBuilderInterface
     */
    protected $splittableCheckoutRestResponseBuilder;

    /**
     * @var \FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface
     */
    protected $splittableCheckoutRestApiClient;

    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface
     */
    protected $splittableCheckoutRequestAttributesExpander;

    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface
     */
    protected $splittableCheckoutRequestValidator;

    /**
     * @param \FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface $splittableCheckoutRestApiClient
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface $splittableCheckoutRequestValidator
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface $splittableCheckoutRequestAttributesExpander
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RestResponseBuilder\SplittableCheckoutRestResponseBuilderInterface $splittableCheckoutRestResponseBuilder
     */
    public function __construct(
        SplittableCheckoutRestApiClientInterface $splittableCheckoutRestApiClient,
        SplittableCheckoutRequestValidatorInterface $splittableCheckoutRequestValidator,
        SplittableCheckoutRequestAttributesExpanderInterface $splittableCheckoutRequestAttributesExpander,
        SplittableCheckoutRestResponseBuilderInterface $splittableCheckoutRestResponseBuilder
    ) {
        $this->splittableCheckoutRequestAttributesExpander = $splittableCheckoutRequestAttributesExpander;
        $this->splittableCheckoutRequestValidator = $splittableCheckoutRequestValidator;
        $this->splittableCheckoutRestApiClient = $splittableCheckoutRestApiClient;
        $this->splittableCheckoutRestResponseBuilder = $splittableCheckoutRestResponseBuilder;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function placeOrder(
        RestRequestInterface $restRequest,
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestResponseInterface {

        $restErrorCollectionTransfer = $this->splittableCheckoutRequestValidator
            ->validateSplittableCheckoutRequest($restRequest, $restSplittableCheckoutRequestAttributesTransfer);
        if ($restErrorCollectionTransfer->getRestErrors()->count()) {
            return $this->createValidationErrorResponse($restErrorCollectionTransfer);
        }

        $restSplittableCheckoutRequestAttributesTransfer = $this->splittableCheckoutRequestAttributesExpander
            ->expandSplittableCheckoutRequestAttributes($restRequest, $restSplittableCheckoutRequestAttributesTransfer);

        $restSplittableCheckoutResponseTransfer = $this->splittableCheckoutRestApiClient
            ->placeOrder($restSplittableCheckoutRequestAttributesTransfer);
        if ($restSplittableCheckoutResponseTransfer->getIsSuccess() === false) {
            /*return $this->createPlaceOrderFailedErrorResponse(
                $restCheckoutMultipleResponseTransfer->getErrors(),
                $restRequest->getMetadata()->getLocale()
            );*/
        }

        return $this->splittableCheckoutRestResponseBuilder
            ->createSplittableCheckoutRestResponse(
                $restSplittableCheckoutResponseTransfer
            );
    }
}
