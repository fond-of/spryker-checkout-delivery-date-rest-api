<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade;

use Generated\Shared\Transfer\QuoteTransfer;

interface SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function placeOrder(QuoteTransfer $quoteTransfer);
}
