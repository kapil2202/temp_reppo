<?php
namespace Aheadworks\CustGroupCatPermissions\Observer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;

/**
 * Class CategoryCollectionLoadBefore
 * @package Aheadworks\CustGroupCatPermissions\Observer
 */
class CategoryCollectionLoadBefore implements ObserverInterface
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

        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $this->permissionApplier->applyForCollection($categoryCollection);

        return $this;
    }
}
