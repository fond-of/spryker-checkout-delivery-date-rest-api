<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\RequestAttributesExpander;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapper;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapperInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class SplittableCheckoutRequestAttributesExpander implements SplittableCheckoutRequestAttributesExpanderInterface
{
    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig
     */
    protected $config;

    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Customer\CustomerMapper
     */
    protected $customerMapper;

    /**
     * SplittableCheckoutRequestAttributesExpander constructor.
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig $config
     */
    public function __construct(
        CustomerMapper $customerMapper,
        SplittableCheckoutRestApiConfig $config
    ) {
        $this->customerMapper = $customerMapper;
        $this->config = $config;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer
     */
    public function expandSplittableCheckoutRequestAttributes(
        RestRequestInterface $restRequest,
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutRequestAttributesTransfer {

        $restSplittableCheckoutRequestAttributesTransfer =
            $this->expandCustomerData($restRequest, $restSplittableCheckoutRequestAttributesTransfer);
        $restSplittableCheckoutRequestAttributesTransfer =
            $this->expandPaymentSelection($restSplittableCheckoutRequestAttributesTransfer);

        return $restSplittableCheckoutRequestAttributesTransfer;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer
     */
    protected function expandCustomerData(
        RestRequestInterface $restRequest,
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutRequestAttributesTransfer {
        $restCustomerTransfer = $this->customerMapper
            ->mapRestCustomerTransferFromRestSplittableCheckoutRequest($restRequest, $restSplittableCheckoutRequestAttributesTransfer);
        $restSplittableCheckoutRequestAttributesTransfer->setCustomer($restCustomerTransfer);

        return $restSplittableCheckoutRequestAttributesTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer
     */
    protected function expandPaymentSelection(
        RestSplittableCheckoutRequestAttributesTransfer $restSplittableCheckoutRequestAttributesTransfer
    ): RestSplittableCheckoutRequestAttributesTransfer {
        $payments = $restSplittableCheckoutRequestAttributesTransfer->getPayments();
        $paymentProviderMethodToStateMachineMapping = $this->config->getPaymentProviderMethodToStateMachineMapping();

        foreach ($payments as $payment) {
            if (isset($paymentProviderMethodToStateMachineMapping[$payment->getPaymentProviderName()][$payment->getPaymentMethodName()])) {
                $payment->setPaymentSelection($paymentProviderMethodToStateMachineMapping[$payment->getPaymentProviderName()][$payment->getPaymentMethodName()]);
            }
        }

        return $restSplittableCheckoutRequestAttributesTransfer;
    }
}
