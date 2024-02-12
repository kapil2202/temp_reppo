<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps;

use Magento\Ui\Block\Component\StepsWizard\StepAbstract as MagentoUiStepAbstract;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class StepAbstract
 *
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Bulk\Wizard\Update\Steps
 */
abstract class StepAbstract extends MagentoUiStepAbstract
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
    }

    /**
     * Retrieve serialized data
     *
     * @param mixed $data
     * @return string
     */
    public function serializeData($data)
    {
        try {
            $serializedData = $this->serializer->serialize($data);
            if (!is_string($serializedData)) {
                $serializedData = "";
            }
        } catch (\InvalidArgumentException $exception) {
            $serializedData = "";
        }
        return $serializedData;
    }
}
