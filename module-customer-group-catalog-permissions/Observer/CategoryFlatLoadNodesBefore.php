<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Observer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;

/**
 * Class CategoryFlatLoadNodesBefore
 */
class CategoryFlatLoadNodesBefore implements ObserverInterface
{
    /**
     * CategoryFlatLoadNodesBefore constructor.
     *
     * @param Config $config
     * @param Applier $permissionApplier
     */
    public function __construct(
        private Config $config,
        private Applier $permissionApplier
    ) {
    }

    /**
     * Apply permissions for load nodes
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isEnabled()) {
            $select = $observer->getData('select');
            $this->permissionApplier->applyForLoadNodesDbSelect($select);
        }
    }
}
