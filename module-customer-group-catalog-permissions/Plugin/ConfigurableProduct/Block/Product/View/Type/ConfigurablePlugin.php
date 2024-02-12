<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableViewBlock;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier as ProductPermissionApplier;

/**
 * Class ConfigurablePlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\ConfigurableProduct\Block\Product\View\Type
 */
class ConfigurablePlugin
{
    /**
     * Add current category id to prevent swatches caching for correct permissions applying
     *
     * @param ConfigurableViewBlock $configurableViewBlock
     * @param array $cacheKeyInfo
     * @return array
     */
    public function afterGetCacheKeyInfo($configurableViewBlock, $cacheKeyInfo)
    {
        /** @var Product $currentProduct */
        $currentProduct = $configurableViewBlock->getProduct();
        if (is_object($currentProduct)) {
            /** @var Category $currentCategory */
            $currentCategory = $currentProduct->getCategory();
            if (is_object($currentCategory)) {
                $cacheKeyInfo[] = $currentCategory->getId();
            }
        }
        return $cacheKeyInfo;
    }

    /**
     * Hide swatches for configurable product, which is not salable due to applied permissions
     *
     * @param ConfigurableViewBlock $configurableViewBlock
     * @param array $allowProducts
     * @return array
     */
    public function afterGetAllowProducts($configurableViewBlock, $allowProducts)
    {
        $result = $allowProducts;
        /** @var Product $currentProduct */
        $currentProduct = $configurableViewBlock->getProduct();
        if (is_object($currentProduct)) {
            if (!$currentProduct->isSalable()) {
                if ($this->isNeedToHideChildProducts($currentProduct)) {
                    $result = [];
                }
            }
        }
        return $result;
    }

    /**
     * Check if need to hide child products for the current configurable
     *
     * @param Product $configurableProduct
     * @return bool
     */
    protected function isNeedToHideChildProducts($configurableProduct)
    {
        return (
            ($configurableProduct->getData(ProductPermissionApplier::PERMISSION_APPLIED))
            && (
                ($configurableProduct->getData(ProductPermissionApplier::HIDE_PRICE))
                || ($configurableProduct->getData(ProductPermissionApplier::HIDE_ADD_TO_CART))
            )
        );
    }
}
