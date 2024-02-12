<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;

/**
 * Class CollectionPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Model\ResourceModel\Product
 */
class CollectionPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Applier
     */
    private $permissionApplier;

    /**
     * @param Config $config
     * @param Applier $permissionApplier
     */
    public function __construct(
        Config $config,
        Applier $permissionApplier
    ) {
        $this->config = $config;
        $this->permissionApplier = $permissionApplier;
    }

    /**
     * @param Collection $productCollection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(Collection $productCollection, $printQuery = false, $logQuery = false)
    {
        if (!$this->config->isEnabled()) {
            return [$printQuery, $logQuery];
        }

        $this->permissionApplier->applyForCollection($productCollection);

        return [$printQuery, $logQuery];
    }

    /**
     * @param Collection $productCollection
     */
    public function beforeGetSize(Collection $productCollection)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->permissionApplier->applyForCollection($productCollection);
    }
}
