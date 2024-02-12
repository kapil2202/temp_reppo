<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps;

use Magento\Framework\View\Element\Template\Context;
use Aheadworks\CustGroupCatPermissions\Model\Source\Wizard\EntityType as WizardEntityTypeSource;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class SelectEntityType
 *
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps
 */
class SelectEntityType extends StepAbstract
{
    const PREDEFINED_ENTITY_TYPE_REQUEST_PARAM_KEY = 'predefinedEntityType';

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::bulk/wizard/update/steps/select_entity_type.phtml';

    /**
     * @var WizardEntityTypeSource
     */
    private $wizardEntityTypeSource;

    /**
     * @param Context $context
     * @param WizardEntityTypeSource $wizardEntityTypeSource
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        WizardEntityTypeSource $wizardEntityTypeSource,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $serializer, $data);
        $this->wizardEntityTypeSource = $wizardEntityTypeSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Select Catalog Element Type');
    }

    /**
     * Retrieve entity type option array
     *
     * @return array
     */
    public function getEntityTypeOptionArray()
    {
        return $this->wizardEntityTypeSource->toOptionArray();
    }

    /**
     * Retrieve predefined entity type from the current request
     *
     * @return string
     */
    public function getPredefinedEntityType()
    {
        return $this->getRequest()->getParam(self::PREDEFINED_ENTITY_TYPE_REQUEST_PARAM_KEY, '');
    }
}
