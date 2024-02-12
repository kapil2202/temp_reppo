<?php

declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Plugin\Framework\Pricing;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\SaleableInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;

class RenderPlugin
{
    /**
     * Price code to show message instead element
     */
    private const PRICE_CODE_TO_SHOW_MESSAGE_INSTEAD = [
        'final_price'
    ];

    /**
     * Added Hidden price message
     *
     * @param Render $subject
     * @param \Closure $proceed
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return void
     */
    public function aroundRender(
        Render $subject,
        \Closure $proceed,
        $priceCode,
        SaleableInterface $saleableItem,
        array $arguments = []
    ) {
        if ($saleableItem->getData(Applier::HIDE_PRICE)) {
            if ($this->isNeedToShowHiddenPriceMessage($priceCode)) {
                $renderResult = (string)$saleableItem->getData(ProductPermissionInterface::HIDDEN_PRICE_MESSAGE);
            } else {
                $renderResult = '';
            }
            return $renderResult;
        } else {
            return $proceed($priceCode, $saleableItem, $arguments);
        }
    }

    /**
     * Check if need to show hidden price message instead of price with specified code
     *
     * @param string $priceCode
     * @return bool
     */
    protected function isNeedToShowHiddenPriceMessage($priceCode)
    {
        return (in_array($priceCode, self::PRICE_CODE_TO_SHOW_MESSAGE_INSTEAD));
    }
}
