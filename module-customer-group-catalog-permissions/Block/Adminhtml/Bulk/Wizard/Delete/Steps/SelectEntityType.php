<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Delete\Steps;

use Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps\SelectEntityType
    as UpdateSelectEntityType;

/**
 * Class SelectEntityType
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Delete\Steps
 */
class SelectEntityType extends UpdateSelectEntityType
{
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Select Catalog Element Type');
    }
}
