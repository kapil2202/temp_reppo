<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin;

use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Framework\Registry;

/**
 * Class SkipPermissionsPlugin
 */
class SkipPermissionsPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Action constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Framework\App\Action\Action $subject
     */
    public function beforeExecute($subject)
    {
        $this->registry->register(Applier::SKIP_PERMISSION_APPLY, true, true);
    }
}