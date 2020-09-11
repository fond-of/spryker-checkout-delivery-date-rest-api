<?php

namespace FondOfSpryker\Glue\SplittableCheckoutRestApi\Plugin;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig;
use FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiConfig;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestSplittableCheckoutRequestAttributesTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRouteCollectionInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceWithParentPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \FondOfSpryker\Glue\SplittableCheckoutRestApi\SplittableCheckoutRestApiFactory getFactory()
 */
class SplittableCheckoutRestApiResourceRoutePlugin extends AbstractPlugin implements ResourceRoutePluginInterface
{
    /**
     * @api
     *
     * {@inheritdoc}
     *
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRouteCollectionInterface $resourceRouteCollection
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRouteCollectionInterface
     */
    public function configure(ResourceRouteCollectionInterface $resourceRouteCollection): ResourceRouteCollectionInterface
    {
        $resourceRouteCollection
            ->addPost(SplittableCheckoutRestApiConfig::ACTION_SPLITTABLE_CHECKOUT_POST, true);

        return $resourceRouteCollection;
    }

    /**
     * @api
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function getResourceType(): string
    {
        return SplittableCheckoutRestApiConfig::RESOURCE_SPLITTABLE_CHECKOUT;
    }

    /**
     * @api
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function getController(): string
    {
        return SplittableCheckoutRestApiConfig::CONTROLLER_SPLITTABLE_CHECKOUT;
    }

    /**
     * @api
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function getResourceAttributesClassName(): string
    {
        return RestSplittableCheckoutRequestAttributesTransfer::class;
    }
}
