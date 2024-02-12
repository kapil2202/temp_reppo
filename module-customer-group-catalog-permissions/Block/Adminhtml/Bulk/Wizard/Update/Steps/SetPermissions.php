<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps;

/**
 * Class SetPermissions
 *
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps
 */
class SetPermissions extends StepAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::bulk/wizard/update/steps/set_permissions.phtml';

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Set Permissions');
    }
}
