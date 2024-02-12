<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Product\Pricing\Renderer;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolver;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as TypeConfigurable;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\ConfigurableProduct\Plugin\Catalog\Model\Product\Pricing\Renderer\SalableResolver
    as ParentSalableResolverPlugin;

/**
 * Class SalableResolverPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Product\Pricing\Renderer
 */
class SalableResolverPlugin extends ParentSalableResolverPlugin
{
    /**
     * @param SalableResolver $subject
     * @param bool $result
     * @param SaleableInterface $salableItem
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsSalable(
        SalableResolver $subject,
        $result,
        SaleableInterface $salableItem
    ) {
        if ($salableItem->getTypeId() === TypeConfigurable::TYPE_CODE
            && !$salableItem->getHideAddToCart()
        ) {
            $result = parent::afterIsSalable($subject, $result, $salableItem);
        }

        return $result;
    }
}
