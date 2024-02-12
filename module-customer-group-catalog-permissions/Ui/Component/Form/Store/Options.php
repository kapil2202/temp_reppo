<?php
namespace Aheadworks\CustGroupCatPermissions\Ui\Component\Form\Store;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Class Options
 *
 * @package Aheadworks\CustGroupCatPermissions\Ui\Component\Form\Column\Store
 */
class Options extends StoreOptions
{
    /**
     * All Store Views value
     */
    const ALL_STORE_VIEWS = 0;

    /**
     * @var bool
     */
    private $allStoreViewsAdded = false;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        parent::toOptionArray();

        if (!$this->allStoreViewsAdded) {
            $this->addAllStoreToOptions();
        }

        return $this->options;
    }

    /**
     * Add "All Store Views" to options select
     */
    private function addAllStoreToOptions()
    {
        array_unshift(
            $this->options,
            [
                'label' => __('All Store Views'),
                'value' => self::ALL_STORE_VIEWS
            ]
        );
        $this->allStoreViewsAdded = true;
    }
}
