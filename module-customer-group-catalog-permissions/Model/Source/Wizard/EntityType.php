<?php
namespace Aheadworks\CustGroupCatPermissions\Model\Source\Wizard;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class EntityType
 *
 * @package Aheadworks\CustGroupCatPermissions\Model\Source\Wizard
 */
class EntityType implements OptionSourceInterface
{
    /**#@+
     * Entity type values
     */
    const CATEGORY = 'category';
    const PRODUCT = 'product';
    const CMS_PAGE = 'cms_page';
    /**#@-*/

    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            [
                'value' => self::CATEGORY,
                'label' => __('Categories')
            ],
            [
                'value' => self::PRODUCT,
                'label' => __('Products')
            ],
            [
                'value' => self::CMS_PAGE,
                'label' => __('Pages')
            ],
        ];

        return $this->options;
    }
}
