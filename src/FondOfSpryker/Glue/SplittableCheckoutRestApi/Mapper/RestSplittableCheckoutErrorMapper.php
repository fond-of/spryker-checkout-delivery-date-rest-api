<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Mapper;

use FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToGlossaryStorageClientInterface;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutErrorTransfer;

class RestSplittableCheckoutErrorMapper implements RestSplittableCheckoutErrorMapperInterface
{
    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig
     */
    protected $config;

    /**
     * @var \FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToGlossaryStorageClientInterface
     */
    protected $glossaryStorageClient;

    /**
     * RestSplittableCheckoutErrorMapper constructor.
     *
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Mapper\CheckoutRestApiConfig $config
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Dependency\Client\SplittableCheckoutRestApiToGlossaryStorageClientInterface $glossaryStorageClient
     */
    public function __construct(
        SplittableCheckoutRestApiConfig $config,
        SplittableCheckoutRestApiToGlossaryStorageClientInterface $glossaryStorageClient
    ) {
        $this->config = $config;
        $this->glossaryStorageClient = $glossaryStorageClient;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutErrorTransfer $restCheckoutErrorTransfer
     * @param \Generated\Shared\Transfer\RestErrorMessageTransfer $restErrorMessageTransfer
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\RestErrorMessageTransfer
     */
    public function mapLocalizedRestSplittableCheckoutErrorTransferToRestErrorTransfer(
        RestSplittableCheckoutErrorTransfer $restSplittableCheckoutErrorTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer,
        string $locale
    ): RestErrorMessageTransfer {
        return $this->mergeErrorDataWithErrorConfiguration(
            $restSplittableCheckoutErrorTransfer,
            $restErrorMessageTransfer,
            $this->translateCheckoutErrorMessage($restCheckoutErrorTransfer, $locale)->toArray()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\RestSplittableCheckoutErrorTransfer $restSplittableCheckoutErrorTransfer
     * @param \FondOfSpryker\Glue\SplittableCheckoutRestApi\Mapper\RestErrorMessageTransfer $restErrorMessageTransfer
     * @param array $errorData
     *
     * @return \FondOfSpryker\Glue\SplittableCheckoutRestApi\Mapper\RestErrorMessageTransfer
     */
    protected function mergeErrorDataWithErrorConfiguration(
        RestSplittableCheckoutErrorTransfer $restSplittableCheckoutErrorTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer,
        array $errorData
    ): RestErrorMessageTransfer {
        $errorIdentifierMapping = $this->getErrorIdentifierMapping($restSplittableCheckoutErrorTransfer);

        if ($errorIdentifierMapping) {
            $errorData = array_merge($errorIdentifierMapping, array_filter($errorData));
        }

        return $restErrorMessageTransfer->fromArray($errorData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutErrorTransfer $restCheckoutErrorTransfer
     *
     * @return array
     */
    protected function getErrorIdentifierMapping(RestCheckoutErrorTransfer $restCheckoutErrorTransfer): array
    {
        return $this->config->getErrorIdentifierToRestErrorMapping()[$restCheckoutErrorTransfer->getErrorIdentifier()] ?? [];
    }

    /**
     * @param \Generated\Shared\Transfer\RestCheckoutErrorTransfer $restCheckoutErrorTransfer
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\RestCheckoutErrorTransfer
     */
    protected function translateCheckoutErrorMessage(
        RestSplittableCheckoutErrorTransfer $restSplittableCheckoutErrorTransfer,
        string $locale
    ): RestSplittableCheckoutErrorTransfer {
        if (!$restSplittableCheckoutErrorTransfer->getDetail()) {
            return $restSplittableCheckoutErrorTransfer;
        }

        $restSplittableCheckoutErrorDetail = $this->glossaryStorageClient->translate(
            $restSplittableCheckoutErrorTransfer->getDetail(),
            $locale,
        );

        if (!$restSplittableCheckoutErrorDetail) {
            return $restSplittableCheckoutErrorTransfer;
        }

        return $restSplittableCheckoutErrorTransfer->setDetail($restSplittableCheckoutErrorDetail);
    }
}
