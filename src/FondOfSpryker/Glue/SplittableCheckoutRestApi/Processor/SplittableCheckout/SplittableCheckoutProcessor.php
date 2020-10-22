<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout;

use FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToCheckoutRestApiClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;
use Generated\Shared\Transfer\SplittableCheckoutResponseTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\Kernel\PermissionAwareTrait;

class SplittableCheckoutProcessor implements SplittableCheckoutProcessorInterface
{
    use PermissionAwareTrait;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

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
     * SplittableCheckoutProcessor constructor.
     *
     * @param \FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface $splittableCheckoutRestApiClient
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface $splittableCheckoutRequestValidator
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface $splittableCheckoutRequestAttributesExpander
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     */
    public function __construct(
        SplittableCheckoutRestApiClientInterface $splittableCheckoutRestApiClient,
        SplittableCheckoutRequestValidatorInterface $splittableCheckoutRequestValidator,
        SplittableCheckoutRequestAttributesExpanderInterface $splittableCheckoutRequestAttributesExpander,
        RestResourceBuilderInterface $restResourceBuilder
    ) {
        $this->splittableCheckoutRequestAttributesExpander = $splittableCheckoutRequestAttributesExpander;
        $this->splittableCheckoutRequestValidator = $splittableCheckoutRequestValidator;
        $this->splittableCheckoutRestApiClient = $splittableCheckoutRestApiClient;
        $this->restResourceBuilder = $restResourceBuilder;
    }

    /**
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout\RestResponseInterface
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
        if (!$restSplittableCheckoutResponseTransfer->getIsSuccess()) {
            return $this->createPlaceOrderFailedErrorResponse(
                $restCheckoutMultipleResponseTransfer->getErrors(),
                $restRequest->getMetadata()->getLocale()
            );
        }

        return $this->createPlacedOrderResponse($restSplittableCheckoutResponseTransfer);
    }

    /**
     * @param string $uuid
     * @param \Generated\Shared\Transfer\RestUserTransfer $restUserTransfer
     *
     * @return bool
     */
    protected function hasPermissionToPlaceOrder(string $uuid, RestUserTransfer $restUserTransfer): bool
    {

        $quoteResponseTransfer = $this->findQuoteByUuid($uuid, $restUserTransfer);
        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return false;
        }

        $companyUserResponseTransfer = $this->findCompanyUserByUuid(
            $quoteResponseTransfer->getQuoteTransfer()->getCompanyUserReference()
        );

        if (!$companyUserResponseTransfer->getIsSuccessful()) {
            return false;
        }

        return $this->can(PlaceOrderPermissionPlugin::KEY, $companyUserResponseTransfer->getCompanyUser()->getFkCompany());
    }

    /**
     * @param string $uuid
     * @param \Generated\Shared\Transfer\RestUserTransfer $restUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected function findQuoteByUuid(string $uuid, RestUserTransfer $restUserTransfer): QuoteResponseTransfer
    {
        return $this->cartsRestApiClient->findQuoteByUuid(
            (new QuoteTransfer())
                ->setUuid($uuid)
                ->setCustomerReference($restUserTransfer->getNaturalIdentifier())
                ->setCustomer((new CustomerTransfer())->setCustomerReference($restUserTransfer->getNaturalIdentifier()))
        );
    }

    /**
     * @param string $companyUserReference
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    protected function findCompanyUserByUuid(string $companyUserReference): CompanyUserResponseTransfer
    {
        return $this->companyUserReferenceClient->findCompanyUserByCompanyUserReference(
            (new CompanyUserTransfer())->setCompanyUserReference($companyUserReference)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createPlacedOrderResponse(
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestResponseInterface {

        $restSplittableCheckoutResponseAttributesTransfer = new RestCheckoutResponseAttributesTransfer();
        $restSplittableCheckoutResponseAttributesTransfer
            ->setOrderReference($restSplittableCheckoutResponseTransfer->getOrderReferences());

        $restResource = $this->restResourceBuilder->createRestResource(
            SplittableCheckoutRestApiConfig::RESOURCE_SPLITTABLE_CHECKOUT,
            null,
            $restSplittableCheckoutResponseAttributesTransfer
        );

        return $this->restResourceBuilder
            ->createRestResponse()
            ->addResource($restResource);
    }
}
