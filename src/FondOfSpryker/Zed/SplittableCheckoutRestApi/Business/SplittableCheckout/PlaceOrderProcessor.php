<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCalculationFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartsRestApiFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCheckoutFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToQuoteFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface;
use Generated\Shared\Transfer\CheckoutDataTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;
use Generated\Shared\Transfer\SplittableCheckoutDataTransfer;
use Generated\Shared\Transfer\SplittableCheckoutResponseTransfer;

class PlaceOrderProcessor implements PlaceOrderProcessorInterface
{
    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartsRestApiFacadeInterface
     */
    protected $cartFacade;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface
     */
    protected $splittableCheckoutFacade;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToQuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCalculationFacadeInterface
     */
    protected $calculationFacade;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[]
     */
    protected $quoteMapperPlugins;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutDataValidatorPluginInterface[]
     */
    protected $splittableCheckoutDataValidatorPlugins;

    /**
     * PlaceOrderProcessor constructor.
     *
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface $quoteReader
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCartsRestApiFacadeInterface $cartFacade
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCheckoutFacadeInterface $checkoutFacade
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToQuoteFacadeInterface $quoteFacade
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToCalculationFacadeInterface $calculationFacade
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[] $quoteMapperPlugins
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutDataValidatorPluginInterface[] $splittableCheckoutDataValidatorPlugins
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        SplittableCheckoutRestApiToCartFacadeInterface $cartFacade,
        SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface $splittableCheckoutFacade,
        SplittableCheckoutRestApiToQuoteFacadeInterface $quoteFacade,
        SplittableCheckoutRestApiToCalculationFacadeInterface $calculationFacade,
        array $quoteMapperPlugins,
        array $splittableCheckoutDataValidatorPlugins
    ) {
        $this->quoteReader = $quoteReader;
        $this->cartFacade = $cartFacade;
        $this->splittableCheckoutFacade = $splittableCheckoutFacade;
        $this->quoteFacade = $quoteFacade;
        $this->calculationFacade = $calculationFacade;
        $this->quoteMapperPlugins = $quoteMapperPlugins;
        $this->splittableCheckoutDataValidatorPlugins = $splittableCheckoutDataValidatorPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    public function placeOrder(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutResponseTransfer {
        $splittableCheckoutResponseTransfer = $this->validateSplittableCheckoutData($restSplittableCheckoutRequestAttributesTransfer);
        if (!$splittableCheckoutResponseTransfer->getIsSuccess()) {
            return $this->createPlaceOrderErrorResponse($splittableCheckoutResponseTransfer);
        }

        $quoteTransfer = $this->quoteReader->findCustomerQuoteByUuid($restSplittableCheckoutRequestAttributesTransfer);

        $restCheckoutResponseTransfer = $this->validateQuoteTransfer($quoteTransfer);
        if ($restCheckoutResponseTransfer !== null) {
            return $restCheckoutResponseTransfer;
        }

        $quoteTransfer = $this->mapRestSplittableCheckoutRequestAttributesToQuote(
            $restSplittableCheckoutRequestAttributesTransfer,
            $quoteTransfer
        );

        $quoteTransfer = $this->recalculateQuote($quoteTransfer);

        $splittableCheckoutResponseTransfer = $this->executePlaceOrder($quoteTransfer);
        if ($splittableCheckoutResponseTransfer->getIsSuccess() === false) {
            return $this->createPlaceOrderErrorResponse($splittableCheckoutResponseTransfer);
        }

        return $this->createRestSplittableCheckoutResponseTransfer($splittableCheckoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer|null $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer|null
     */
    protected function validateQuoteTransfer(?QuoteTransfer $quoteTransfer): ?RestCheckoutResponseTransfer
    {
        if (!$quoteTransfer) {
            return $this->createCartNotFoundErrorResponse();
        }

        if (!count($quoteTransfer->getItems())) {
            return $this->createCartIsEmptyErrorResponse();
        }

        $quoteResponseTransfer = $this->cartFacade->validateQuote($quoteTransfer);

        if ($quoteResponseTransfer->getIsSuccessful() === false) {
            return $this->createQuoteResponseError(
                $quoteResponseTransfer,
                CheckoutRestApiConfig::ERROR_IDENTIFIER_CHECKOUT_DATA_INVALID
            );
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer
     */
    protected function validateSplittableCheckoutData(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): SplittableCheckoutResponseTransfer {
        $splittableCheckoutResponseTransfer = (new SplittableCheckoutResponseTransfer())->setIsSuccess(true);
        $splittableCheckoutDataTransfer = (new SplittableCheckoutDataTransfer())
            ->fromArray($restSplittableCheckoutRequestAttributesTransfer->toArray(), true);

        foreach ($this->splittableCheckoutDataValidatorPlugins as $splittableCheckoutDataValidatorPlugin) {
            $validatedSplittableCheckoutData = $splittableCheckoutDataValidatorPlugin->validateSplittableCheckoutData($splittableCheckoutDataTransfer);
            if (!$validatedSplittableCheckoutData->getIsSuccess()) {
                $splittableCheckoutResponseTransfer = $this->appendSplittableCheckoutResponseErrors(
                    $validatedSplittableCheckoutData,
                    $splittableCheckoutResponseTransfer
                );
            }
        }

        return $splittableCheckoutResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer $validatedSplittableCheckoutData
     * @param \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer
     */
    protected function appendSplittableCheckoutResponseErrors(
        SplittableCheckoutResponseTransfer $validatedSplittableCheckoutData,
        SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
    ): SplittableCheckoutResponseTransfer {
        foreach ($validatedSplittableCheckoutData->getErrors() as $splittableCheckoutErrorTransfer) {
            $splittableCheckoutResponseTransfer->getErrors()->append($splittableCheckoutErrorTransfer);
        }

        return $splittableCheckoutResponseTransfer->setIsSuccess(false);
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function mapRestSplittableCheckoutRequestAttributesToQuote(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer {
        foreach ($this->quoteMapperPlugins as $quoteMapperPlugin) {
            $restCheckoutRequestAttributesTransfer =
                (new RestCheckoutRequestAttributesTransfer())->fromArray($restSplittableCheckoutRequestAttributesTransfer->toArray());
            $quoteTransfer = $quoteMapperPlugin->map($restCheckoutRequestAttributesTransfer, $quoteTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function recalculateQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->calculationFacade->recalculateQuote($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer
     */
    protected function executePlaceOrder(QuoteTransfer $quoteTransfer): SplittableCheckoutResponseTransfer
    {
        return $this->splittableCheckoutFacade->placeOrder($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     * @param string $errorIdentifier
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    protected function createQuoteResponseError(
        QuoteResponseTransfer $quoteResponseTransfer,
        string $errorIdentifier
    ): RestSplittableCheckoutResponseTransfer {
        if ($quoteResponseTransfer->getErrors()->count() === 0) {
            return (new RestCheckoutResponseTransfer())
                ->setIsSuccess(false)
                ->addError(
                    (new RestSplittableCheckoutErrorTransfer())
                        ->setErrorIdentifier($errorIdentifier)
                );
        }

        $restSplittableCheckoutResponseTransfer = (new RestSplittableCheckoutResponseTransfer())
            ->setIsSuccess(false);
        foreach ($quoteResponseTransfer->getErrors() as $quoteErrorTransfer) {
            $restSplittableCheckoutResponseTransfer->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier($errorIdentifier)
                    ->setDetail($quoteErrorTransfer->getMessage())
            );
        }

        return $restSplittableCheckoutResponseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    protected function createCartNotFoundErrorResponse(): RestSplittableCheckoutResponseTransfer
    {
        return (new RestSplittableCheckoutResponseTransfer())
            ->setIsSuccess(false)
            ->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_CART_NOT_FOUND)
            );
    }

    /**
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    protected function createCartIsEmptyErrorResponse(): RestSplittableCheckoutResponseTransfer
    {
        return (new RestSplittableCheckoutResponseTransfer())
            ->setIsSuccess(false)
            ->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_CART_IS_EMPTY)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    protected function createPlaceOrderErrorResponse(
        SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
    ): RestSplittableCheckoutResponseTransfer
    {
        if ($splittableCheckoutResponseTransfer->getErrors()->count() === 0) {
            return (new RestSplittableCheckoutResponseTransfer())
                ->setIsSuccess(false)
                ->addError(
                    (new RestSplittableCheckoutErrorTransfer())
                        ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_ORDER_NOT_PLACED)
                );
        }
        $restSplittableCheckoutResponseTransfer = (new RestSplittableCheckoutResponseTransfer())
            ->setIsSuccess(false);
        foreach ($splittableCheckoutResponseTransfer->getErrors() as $errorTransfer) {
            $restSplittableCheckoutResponseTransfer->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_ORDER_NOT_PLACED)
                    ->setDetail($errorTransfer->getMessage())
                    ->setParameters($errorTransfer->getParameters())
            );
        }

        return $restSplittableCheckoutResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    protected function createRestSplittableCheckoutResponseTransfer(
        SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
    ): RestSplittableCheckoutResponseTransfer {
        return (new RestSplittableCheckoutResponseTransfer())
            ->setIsSuccess(true)
            ->setOrderReferences($splittableCheckoutResponseTransfer->getOrderReferences());
    }

}
