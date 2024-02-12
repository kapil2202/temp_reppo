<?php
namespace Aheadworks\CustGroupCatPermissions\Observer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductCollectionLoadAfter
 * @package Aheadworks\CustGroupCatPermissions\Observer
 */
class ProductCollectionLoadAfter implements ObserverInterface
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
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return $this;
        }

        /** @var Collection $productCollection */
        $productCollection = $observer->getEvent()->getCollection();
        /** @var ProductInterface $product */
        foreach ($productCollection->getItems() as $product) {
            $this->permissionApplier->applyForProduct($product);
        }

        return $this;
    }
}
