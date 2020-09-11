<?php

namespace FondOfSpryker\Client\SplittableCheckoutRestApi;

use Generated\Shared\Transfer\RestCheckoutDataResponseTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCheckoutResponseTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;

interface SplittableCheckoutRestApiClientInterface
{
    /**
     * Specification:
     * - Looks up the customer quote by uuid.
     * - Validates quote.
     * - Executes plugins that maps request data into QuoteTransfer.
     * - Recalculates quote.
     * - Places an order.
     * - Deletes quote if order was placed successfully.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    public function placeOrder(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutResponseTransfer;
}
