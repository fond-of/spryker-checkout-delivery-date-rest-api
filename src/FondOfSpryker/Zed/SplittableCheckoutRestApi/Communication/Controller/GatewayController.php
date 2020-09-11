<?php

namespace FondOfSpryker\Zed\SplittableCheckoutRestApi\Communication\Controller;

use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \FondOfSpryker\Zed\SplittableCheckoutRestApi\Business\SplittableCheckoutRestApiFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \FondOfSpryker\Zed\SplittableCheckoutRestApi\Communication\Controller\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutResponseTransfer
     */
    public function placeOrderAction(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutResponseTransfer {
        return $this->getFacade()->placeOrder($restSplittableCheckoutRequestAttributesTransfer);
    }
}
