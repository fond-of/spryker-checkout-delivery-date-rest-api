<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator;

use FondOfSpryker\Shared\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface;
use Generated\Shared\Transfer\RestSplittableCheckoutDataResponseTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;
use Generated\Shared\Transfer\SplittableCheckoutDataTransfer;
use Generated\Shared\Transfer\SplittableCheckoutResponseTransfer;

class SplittableCheckoutValidator implements SplittableCheckoutValidatorInterface
{
    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutDataValidatorPluginInterface[]
     */
    protected $splittableCheckoutDataValidatorPlugins;

    /**
     * SplittableCheckoutValidator constructor.
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Quote\QuoteReaderInterface $quoteReader
     *
     * @param array $splittableCheckoutDataValidatorPlugins
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        array $splittableCheckoutDataValidatorPlugins
    ) {
        $this->quoteReader = $quoteReader;
        $this->splittableCheckoutDataValidatorPlugins = $splittableCheckoutDataValidatorPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator\RestSplittableCheckoutDataResponseTransfer
     */
    public function validateSplittableCheckout(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutResponseTransfer {
        $quoteTransfer = $this->quoteReader->findCustomerQuoteByUuid($restSplittableCheckoutRequestAttributesTransfer);
        $restSplittableCheckoutResponseTransfer = new RestSplittableCheckoutResponseTransfer();

        $splittableCheckoutDataTransfer = (new SplittableCheckoutDataTransfer())
            ->fromArray($restSplittableCheckoutRequestAttributesTransfer->toArray(), true)
            ->setQuote($quoteTransfer);

        $restSplittableCheckoutResponseTransfer = $this->executeSplittableCheckoutDataValidatorPlugins(
            $splittableCheckoutDataTransfer,
            $restSplittableCheckoutResponseTransfer
        );

        return $this->getRestSplittableCheckoutResponse(
            $splittableCheckoutDataTransfer,
            $restSplittableCheckoutResponseTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\SplittableCheckoutDataTransfer $splittableCheckoutDataTransfer
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    protected function executeSplittableCheckoutDataValidatorPlugins(
        SplittableCheckoutDataTransfer $splittableCheckoutDataTransfer,
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestSplittableCheckoutResponseTransfer {
        foreach ($this->splittableCheckoutDataValidatorPlugins as $splittableCheckoutDataValidatorPlugin) {
            $splittableCheckoutResponseTransfer = $splittableCheckoutDataValidatorPlugin
                ->validateSplittableCheckoutData($splittableCheckoutDataTransfer);

            if ($splittableCheckoutResponseTransfer->getIsSuccess() === false) {
                $restSplittableCheckoutResponseTransfer = $this->copySplittableCheckoutResponseErrors(
                    $splittableCheckoutResponseTransfer,
                    $restSplittableCheckoutResponseTransfer
                );
            }
        }

        return $restSplittableCheckoutResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator\RestSplittableCheckoutDataResponseTransfer $restSplittableCheckoutDataResponseTransfer
     * 
     * @return \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\Validator\RestSplittableCheckoutDataResponseTransfer
     */
    protected function copySplittableCheckoutDataResponseErrors(
        SplittableCheckoutResponseTransfer $splittableCheckoutResponseTransfer,
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestSplittableCheckoutResponseTransfer {
        foreach ($splittableCheckoutResponseTransfer->getErrors() as $splittableCheckoutErrorTransfer) {
            $restSplittableCheckoutResponseTransfer->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_ORDER_NOT_PLACED)
                    ->setDetail($splittableCheckoutErrorTransfer->getMessage())
            );
        }

        return $restSplittableCheckoutResponseTransfer->setIsSuccess(false);
    }


    /**
     * @param \Generated\Shared\Transfer\CheckoutDataTransfer $checkoutDataTransfer
     * @param \Generated\Shared\Transfer\RestCheckoutResponseTransfer $restCheckoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutResponseTransfer
     */
    protected function getRestSplittableCheckoutResponse(
        SplittableCheckoutDataTransfer $splittableCheckoutDataTransfer,
        RestSplittableCheckoutResponseTransfer $restSplittableCheckoutResponseTransfer
    ): RestSplittableCheckoutResponseTransfer {
        $restSplittableCheckoutResponseTransfer->setSplittableCheckoutData($splittableCheckoutDataTransfer);

        if ($restSplittableCheckoutResponseTransfer->getIsSuccess() === true
            || $restSplittableCheckoutResponseTransfer->getErrors()->count() === 0
        ) {
            return $restSplittableCheckoutResponseTransfer->setIsSuccess(true);
        }

        return $restSplittableCheckoutResponseTransfer
            ->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_ORDER_NOT_PLACED)
            );
    }


}
