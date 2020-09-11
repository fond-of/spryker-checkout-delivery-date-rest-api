<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Address\AddressReaderInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToPaymentFacadeInterface;
use FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToShipmentFacadeInterface;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutDataResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutDataTransfer;
use Generated\Shared\Transfer\RestCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutDataResponseTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutDataTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutErrorTransfer;
use Generated\Shared\Transfer\ShipmentMethodsTransfer;

class SplittableCheckoutDataReader implements SplittableCheckoutDataReaderInterface
{
    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToShipmentFacadeInterface
     */
    protected $shipmentFacade;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToPaymentFacadeInterface
     */
    protected $paymentFacade;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Address\AddressReaderInterface
     */
    protected $addressReader;

    /**
     * @var \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[]
     */
    protected $quoteMapperPlugins;

    /**
     * SplittableCheckoutDataReader constructor.
     *
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\QuoteReaderInterface $quoteReader
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToShipmentFacadeInterface $shipmentFacade
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade\SplittableCheckoutRestApiToPaymentFacadeInterface $paymentFacade
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckout\Address\AddressReaderInterface $addressReader
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApiExtension\Dependency\Plugin\QuoteMapperPluginInterface[] $quoteMapperPlugins
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        SplittableCheckoutRestApiToShipmentFacadeInterface $shipmentFacade,
        SplittableCheckoutRestApiToPaymentFacadeInterface $paymentFacade,
        AddressReaderInterface $addressReader,
        array $quoteMapperPlugins
    ) {
        $this->quoteReader = $quoteReader;
        $this->shipmentFacade = $shipmentFacade;
        $this->paymentFacade = $paymentFacade;
        $this->addressReader = $addressReader;
        $this->quoteMapperPlugins = $quoteMapperPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutDataResponseTransfer
     */
    public function getSplittableCheckoutData(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutDataResponseTransfer {
        $quoteTransfer = $this->quoteReader->findCustomerQuoteByUuid($restSplittableCheckoutRequestAttributesTransfer);

        if (!$quoteTransfer) {
            return $this->createCartNotFoundErrorResponse();
        }

        foreach ($this->quoteMapperPlugins as $quoteMappingPlugin) {
            $quoteTransfer = $quoteMappingPlugin->map($restSplittableCheckoutRequestAttributesTransfer, $quoteTransfer);
        }

        $splittableCheckoutDataTransfer = (new RestSplittableCheckoutDataTransfer())
            ->setShipmentMethods($this->getShipmentMethodsTransfer($quoteTransfer))
            ->setPaymentProviders($this->getPaymentProviders())
            ->setAddresses($this->addressReader->getAddressesTransfer($quoteTransfer))
            ->setAvailablePaymentMethods($this->getAvailablePaymentMethods($quoteTransfer));

        return (new RestSplittableCheckoutDataResponseTransfer())
                ->setIsSuccess(true)
                ->setCheckoutData($checkoutDataTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodsTransfer
     */
    protected function getShipmentMethodsTransfer(QuoteTransfer $quoteTransfer): ShipmentMethodsTransfer
    {
        return $this->shipmentFacade->getAvailableMethods($quoteTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionTransfer
     */
    protected function getPaymentProviders(): PaymentProviderCollectionTransfer
    {
        return $this->paymentFacade->getAvailablePaymentProviders();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): PaymentMethodsTransfer
    {
        return $this->paymentFacade->getAvailableMethods($quoteTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutDataResponseTransfer
     */
    protected function createCartNotFoundErrorResponse(): RestSplittableCheckoutDataResponseTransfer
    {
        return (new RestSplittableCheckoutDataResponseTransfer())
            ->setIsSuccess(false)
            ->addError(
                (new RestSplittableCheckoutErrorTransfer())
                    ->setErrorIdentifier(SplittableCheckoutRestApiConfig::ERROR_IDENTIFIER_CART_NOT_FOUND)
            );
    }
}
