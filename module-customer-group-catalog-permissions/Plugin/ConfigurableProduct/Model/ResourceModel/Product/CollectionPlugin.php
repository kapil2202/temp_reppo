<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\ConfigurableProduct\Model\ResourceModel\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;

/**
 * Class CollectionPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\ConfigurableProduct\Model\ResourceModel\Product
 */
class CollectionPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Collection $productCollection
     * @param array $items
     * @return array
     */
    public function afterGetItems(Collection $productCollection, $items)
    {
        if (!$this->config->isEnabled()) {
            return $items;
        }

        /** @var Product $item */
        foreach ($items as $item) {
            $item->unsetData(Applier::PERMISSION_APPLIED);
            $item->unsetData('is_salable');
        }

        return $items;
    }
}
