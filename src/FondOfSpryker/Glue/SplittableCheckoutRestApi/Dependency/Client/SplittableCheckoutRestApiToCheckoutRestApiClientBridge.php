<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToCheckoutRestApiClientInterface;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface;

class SplittableCheckoutRestApiToCheckoutRestApiClientBridge implements SplittableCheckoutRestApiToCheckoutRestApiClientInterface
{
    /**
     * @var \Spryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface
     */
    protected $checkoutRestApiClient;

    /**
     * SplittableCheckoutRestApiToCheckoutRestApiClientBridge constructor.
     *
     * @param \Spryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface $checkoutRestApiClient
     */
    public function __construct(
        CheckoutRestApiClientInterface $checkoutRestApiClient
    ) {
        $this->checkoutRestApiClient = $checkoutRestApiClient;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\RestCheckoutResponseTransfer
     */
    public function placeOrder(
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
    ): RestCheckoutResponseTransfer {
        return $this->checkoutRestApiClient->placeOrder($restCheckoutRequestAttributesTransfer);
    }
}
