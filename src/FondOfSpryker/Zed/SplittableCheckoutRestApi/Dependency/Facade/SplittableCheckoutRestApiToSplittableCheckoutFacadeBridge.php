<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Dependency\Facade;

use FondOfSpryker\Zed\SplittableCheckout\Business\SplittableCheckoutFacadeInterface;
use Generated\Shared\Transfer\QuoteTransfer;

class SplittableCheckoutRestApiToSplittableCheckoutFacadeBridge implements SplittableCheckoutRestApiToSplittableCheckoutFacadeInterface
{
    /**
     * @var \FondOfSpryker\Zed\SplittableCheckout\Business\SplittableCheckoutFacadeInterface
     */
    protected $splitttableCheckoutFacade;

    /**
     * SplittableCheckoutRestApiToSplittableCheckoutFacadeBridge constructor.
     *
     * @param \FondOfSpryker\Zed\SplittableCheckout\Business\SplittableCheckoutFacadeInterface $splittableCheckoutFacade
     */
    public function __construct(SplittableCheckoutFacadeInterface $splittableCheckoutFacade)
    {
        $this->splitttableCheckoutFacade = $splittableCheckoutFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function placeOrder(QuoteTransfer $quoteTransfer)
    {
        return $this->splitttableCheckoutFacade->placeOrder($quoteTransfer);
    }
}
