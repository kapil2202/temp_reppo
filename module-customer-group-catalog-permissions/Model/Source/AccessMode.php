<?php
namespace Aheadworks\CustGroupCatPermissions\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AccessMode
 * @package Aheadworks\CustGroupCatPermissions\Model\Source
 */
class AccessMode implements OptionSourceInterface
{
    /**#@+
     * Email status values
     */
    const SHOW = 1;
    const HIDE = 2;
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
                'value' => self::SHOW,
                'label' => __('Show')
            ],
            [
                'value' => self::HIDE,
                'label' => __('Hide')
            ]
        ];

        return $this->options;
    }
}
