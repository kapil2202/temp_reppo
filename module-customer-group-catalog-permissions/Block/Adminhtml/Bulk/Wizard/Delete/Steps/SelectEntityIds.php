<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Delete\Steps;

use Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps\SelectEntityIds
    as UpdateSelectEntityIds;

/**
 * Class SelectEntityIds
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Delete\Steps
 */
class SelectEntityIds extends UpdateSelectEntityIds
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::bulk/wizard/delete/steps/select_entity_ids.phtml';
}
