<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\GroupedProduct\Model\Product\Type;

use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Model\Product;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;

/**
 * Class GroupedPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\GroupedProduct\Model\Product\Type
 */
class GroupedPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Additional check of the product to hide 'Add to cart button',
     * when all associated products are hidden due to permissions
     *
     * @param GroupedProductType $typeInstance
     * @param bool $isSalable
     * @param Product $product
     * @return bool
     */
    public function afterIsSalable($typeInstance, $isSalable, $product)
    {
        if (!$this->config->isEnabled()) {
            return $isSalable;
        }

        if ($product->getData(Applier::PERMISSION_APPLIED)) {
            if ($this->isNeedToHideAddToCartButton($typeInstance, $product)) {
                $isSalable = false;
                $product->setIsSalable($isSalable);
                $product->setData(Applier::HIDE_ADD_TO_CART, true);
            }
        }

        return $isSalable;
    }

    /**
     * Check if need to hide 'Add to cart' button, when all associated products are hidden due to permissions
     *
     * @param GroupedProductType $typeInstance
     * @param Product $product
     * @return bool
     */
    protected function isNeedToHideAddToCartButton($typeInstance, $product)
    {
        $associatedProducts = $typeInstance->getAssociatedProducts($product);
        $result = true;

        foreach ($associatedProducts as $associatedProduct) {
            if ($associatedProduct->getIsSalable()) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
