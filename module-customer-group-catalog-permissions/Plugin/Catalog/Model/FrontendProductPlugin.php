<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Catalog\Model\Product as ProductModel;

class FrontendProductPlugin
{
    /**
     * @param Config $config
     */
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Additional check of the product to hide 'Add to cart button',
     * when all associated products are hidden due to permissions
     *
     * @param ProductModel $product
     * @param bool $isSaleable
     * @return bool
     */
    public function afterIsSaleable(ProductModel $product, $isSaleable)
    {
        if ($this->config->isEnabled()) {
            if ($product->getData(Applier::PERMISSION_APPLIED)
                && ($product->getData(Applier::HIDE_PRICE)
                || $product->getData(Applier::HIDE_ADD_TO_CART))) {
                $isSaleable = false;
            }
        }

        return $isSaleable;
    }
}
