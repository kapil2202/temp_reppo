<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\AbstractCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission as ProductPermissionResource;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission as ProductPermissionModel;

/**
 * Class Collection
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = ProductPermissionInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ProductPermissionModel::class, ProductPermissionResource::class);
    }
}
