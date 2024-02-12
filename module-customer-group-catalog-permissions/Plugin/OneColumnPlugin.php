<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Plugin;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\LayeredNavigation\Block\Navigation;

/**
 * Class OneColumnPlugin for permissions apply to display correct one column layout
 */
class OneColumnPlugin
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
     * OneColumnPlugin constructor.
     *
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
     * Apply permissions before show block, if module enabled
     *
     * @param Navigation $navigation
     * @return void
     */
    public function beforeCanShowBlock(Navigation $navigation): void
    {
        if ($this->config->isEnabled()) {
            $productCollection = $navigation->getLayer()->getProductCollection();
            $this->permissionApplier->applyForCollection($productCollection);
        }
    }
}
