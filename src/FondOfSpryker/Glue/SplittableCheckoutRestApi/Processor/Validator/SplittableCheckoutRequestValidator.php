<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Processor\Validator;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use Generated\Shared\Transfer\RestErrorCollectionTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class SplittableCheckoutRequestValidator implements SplittableCheckoutRequestValidatorInterface
{
    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutRequestAttributesValidatorPluginInterface[]
     */
    protected $splittableCheckoutRequestAttributesValidatorPlugins;

    /**
     * SplittableCheckoutRequestValidator constructor.
     *
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApiExtension\Dependency\Plugin\SplittableCheckoutRequestAttributesValidatorPluginInterface[] $splittableCheckoutRequestAttributesValidatorPlugins
     */
    public function __construct(
        array $splittableCheckoutRequestAttributesValidatorPlugins
    ) {
        $this->splittableCheckoutRequestAttributesValidatorPlugins = $splittableCheckoutRequestAttributesValidatorPlugins;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer $splittableCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestErrorCollectionTransfer
     */
    public function validateSplittableCheckoutRequest(
        RestRequestInterface $restRequest,
        RestSplittableCheckoutRequestAttributesTransfer $splittableCheckoutRequestAttributesTransfer
    ): RestErrorCollectionTransfer {
        $restErrorCollectionTransfer = new RestErrorCollectionTransfer();

        foreach ($this->splittableCheckoutRequestAttributesValidatorPlugins as $splittableCheckoutRequestAttributesValidatorPlugin) {
            $pluginErrorCollectionTransfer = $splittableCheckoutRequestAttributesValidatorPlugin
                ->validateAttributes($splittableCheckoutRequestAttributesTransfer);
            foreach ($pluginErrorCollectionTransfer->getRestErrors() as $restError) {
                $restErrorCollectionTransfer->addRestError($restError);
            }
        }

        return $restErrorCollectionTransfer;
    }

}
