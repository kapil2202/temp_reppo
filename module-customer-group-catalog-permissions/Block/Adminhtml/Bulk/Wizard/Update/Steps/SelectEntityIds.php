<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Ui\Component\Product\Form\Categories\Options as CategoriesOptions;

/**
 * Class SelectEntityIds
 *
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps
 */
class SelectEntityIds extends StepAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::bulk/wizard/update/steps/select_entity_ids.phtml';

    /**
     * @var CategoriesOptions
     */
    private $categoriesOptions;

    /**
     * @param Context $context
     * @param CategoriesOptions $categoriesOptions
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoriesOptions $categoriesOptions,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $serializer, $data);
        $this->categoriesOptions = $categoriesOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Select Items');
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    public function getCategoriesTree()
    {
        return $this->categoriesOptions->toOptionArray();
    }
}
