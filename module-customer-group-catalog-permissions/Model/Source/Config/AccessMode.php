<?php
namespace Aheadworks\CustGroupCatPermissions\Model\Source\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AccessMode
 *
 * @package Aheadworks\CustGroupCatPermissions\Model\Source\Config
 */
class AccessMode implements OptionSourceInterface
{
    /**#@+
     * Entity access mode values
     */
    const SHOW_FOR_EVERYONE = 'show_for_everyone';
    const HIDE_FROM_SPECIFIED_CUSTOMER_GROUPS = 'hide_from_specified_customer_groups';
    const HIDE_FROM_EVERYONE = 'hide_from_everyone';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SHOW_FOR_EVERYONE,
                'label' => __('Show for everyone')
            ],
            [
                'value' => self::HIDE_FROM_SPECIFIED_CUSTOMER_GROUPS,
                'label' => __('Hide from specified customer groups')
            ],
            [
                'value' => self::HIDE_FROM_EVERYONE,
                'label' => __('Hide from everyone')
            ]
        ];
    }
}
