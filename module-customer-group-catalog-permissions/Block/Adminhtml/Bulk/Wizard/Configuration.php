<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard;

use Magento\Backend\Block\Template;

/**
 * Class Configuration
 *
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update
 */
class Configuration extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::bulk/wizard/configuration.phtml';

    /**
     * Render bulk update wizard steps
     *
     * @param array $initData
     * @return string
     */
    public function renderBulkUpdateWizardSteps($initData = [])
    {
        /** @var \Magento\Ui\Block\Component\StepsWizard $wizardBlock */
        $wizardBlock = $this->getChildBlock($this->getData('config/stepWizardName'));
        if ($wizardBlock) {
            $wizardBlock->setInitData($initData);
            return $wizardBlock->toHtml();
        }
        return '';
    }
}
