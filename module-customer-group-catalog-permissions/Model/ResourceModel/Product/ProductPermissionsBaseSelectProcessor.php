<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel\Product;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission as ProductPermissionResource;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver;
use Magento\Framework\DB\Select;

/**
 * Class ProductPermissionsBaseSelectProcessor
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel\Product
 */
class ProductPermissionsBaseSelectProcessor implements BaseSelectProcessorInterface
{
    /**
     * @var ProductPermissionResource
     */
    private $resource;

    /**
     * @var Resolver
     */
    private $permissionResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     * @param ProductPermissionResource $resource
     * @param Resolver $resolver
     */
    public function __construct(
        Config $config,
        ProductPermissionResource $resource,
        Resolver $resolver
    ) {
        $this->resource = $resource;
        $this->permissionResolver = $resolver;
        $this->config = $config;
    }

    /**
     * Add product permissions filter to selects
     *
     * @param Select $select
     * @return Select
     */
    public function process(Select $select)
    {
        if (!$this->config->isEnabled()) {
            return $select;
        }

        list($storeId, $customerGroupId, $defaultViewMode) = $this->permissionResolver->getRequiredFilterValues();
        $permissionsSelect = $this->resource->getProductsFilter(
            $storeId,
            $customerGroupId,
            $defaultViewMode,
            true)
        ;
        if(key($permissionsSelect) == 'nin') {
            $select->where('child.entity_id NOT IN (?)', $permissionsSelect);
        }
        else {
            $select->where('child.entity_id IN (?)', $permissionsSelect);
        }

        return $select;
    }
}
