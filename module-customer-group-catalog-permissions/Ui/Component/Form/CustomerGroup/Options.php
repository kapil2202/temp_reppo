<?php
namespace Aheadworks\CustGroupCatPermissions\Ui\Component\Form\CustomerGroup;

use Aheadworks\CustGroupCatPermissions\Model\Source\Customer\Groups as GroupOptions;

/**
 * Class Options
 * @package Aheadworks\CustGroupCatPermissions\Ui\Component\Form\Column\CustomerGroup
 */
class Options extends GroupOptions
{
    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = parent::toOptionArray();
            $this->addAllGroupsToOptions();
        }

        return $this->options;
    }

    /**
     * Add "All Customer Groups" to options select
     */
    private function addAllGroupsToOptions()
    {
        array_unshift(
            $this->options,
            [
                'label' => __('All Customer Groups'),
                'value' => self::ALL_GROUPS
            ]
        );
    }
}
