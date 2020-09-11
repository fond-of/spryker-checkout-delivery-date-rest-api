<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\SplittableCheckout;

use FondOfSpryker\Client\SplittableCheckoutRestApi\SplittableCheckoutRestApiClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToCheckoutRestApiClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander\SplittableCheckoutRequestAttributesExpanderInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\CheckoutRequestValidatorInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator\SplittableCheckoutRequestValidatorInterface;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\Kernel\PermissionAwareTrait;

class SplittableCheckoutProcessor implements SplittableCheckoutProcessorInterface
{
    use PermissionAwareTrait;

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

    public function __construct(
        SplittableCheckoutRestApiClientInterface $splittableCheckoutRestApiClient,
        SplittableCheckoutRequestValidatorInterface $splittableCheckoutRequestValidator,
        SplittableCheckoutRequestAttributesExpanderInterface $splittableCheckoutRequestAttributesExpander
    ) {
        $this->splittableCheckoutRequestAttributesExpander = $splittableCheckoutRequestAttributesExpander;
        $this->splittableCheckoutRequestValidator = $splittableCheckoutRequestValidator;
        $this->splittableCheckoutRestApiClient = $splittableCheckoutRestApiClient;
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

        $restCheckoutResponseTransfer = $this->splittableCheckoutRestApiClient
            ->placeOrder($restSplittableCheckoutRequestAttributesTransfer);
        if (!$restCheckoutResponseTransfer->getIsSuccess()) {
            return $this->createPlaceOrderFailedErrorResponse(
                $restCheckoutMultipleResponseTransfer->getErrors(),
                $restRequest->getMetadata()->getLocale()
            );
        }

        return $this->createOrderPlacedMultipleResponse($restCheckoutMultipleResponseTransfer->getOrderReferences());
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
     * @param string[] $orderReferences
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createOrderPlacedMultipleResponse(array $orderReferences): RestResponseInterface
    {
        $restCheckoutMultipleResponseAttributesTransfer = new RestCheckoutMultipleResponseAttributesTransfer();
        $restCheckoutMultipleResponseAttributesTransfer->setOrderReferences($orderReferences);

        $restResource = $this->restResourceBuilder->createRestResource(
            CheckoutRestApiConfig::RESOURCE_CHECKOUT,
            null,
            $restCheckoutMultipleResponseAttributesTransfer
        );

        return $this->restResourceBuilder
            ->createRestResponse()
            ->addResource($restResource);
    }
}
