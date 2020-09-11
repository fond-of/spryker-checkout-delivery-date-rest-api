<?php

namespace FondOfSpryker\Shared\SplittableCheckoutRestApi;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class SplittableCheckoutRestApi extends AbstractBundleConfig
{
    public const ERROR_IDENTIFIER_CHECKOUT_DATA_INVALID = 'ERROR_IDENTIFIER_CHECKOUT_DATA_INVALID';
    public const ERROR_IDENTIFIER_ORDER_NOT_PLACED = 'ERROR_IDENTIFIER_ORDER_NOT_PLACED';
    public const ERROR_IDENTIFIER_CART_NOT_FOUND = 'ERROR_IDENTIFIER_CART_NOT_FOUND';
    public const ERROR_IDENTIFIER_CART_IS_EMPTY = 'ERROR_IDENTIFIER_CART_IS_EMPTY';
    public const ERROR_IDENTIFIER_UNABLE_TO_DELETE_CART = 'ERROR_IDENTIFIER_UNABLE_TO_DELETE_CART';
}
