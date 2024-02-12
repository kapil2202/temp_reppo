<?php
namespace Aheadworks\CustGroupCatPermissions\Observer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CheckoutProductAddAfter
 * @package Aheadworks\CustGroupCatPermissions\Observer
 */
class CheckoutProductAddAfter implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Applier
     */
    private $permissionApplier;

    /**
     * @param Config $config
     * @param Applier $permissionApplier
     */
    public function __construct(
        Config $config,
        Applier $permissionApplier
    ) {
        $this->config = $config;
        $this->permissionApplier = $permissionApplier;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return $this;
        }

        /** @var QuoteItem $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        $this->permissionApplier->applyForProduct($product);
        $needToRemoveItem = $this->isNeedToRemoveItem($product);
        if ($needToRemoveItem) {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            throw new LocalizedException(
                __('Sorry, you are not allowed to add this product to your cart.')
            );
        }

        return $this;
    }

    /**
     * Check is need to remove item from quote
     *
     * @param Product $product
     * @return bool
     */
    private function isNeedToRemoveItem($product)
    {
        return $product->getData(Applier::HIDE_PRODUCT)
            || $product->getData(Applier::HIDE_PRICE)
            || $product->getData(Applier::HIDE_ADD_TO_CART);
    }
}
