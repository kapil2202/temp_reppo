<?php
namespace Aheadworks\CustGroupCatPermissions\Ui\Component\Form;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Store
 * @package Aheadworks\CustGroupCatPermissions\Ui\Component\Form\Column
 */
class Store extends MultiSelect
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param array|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storeManager,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $options, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ($this->storeManager->hasSingleStore()) {
            $config['visible'] = false;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
