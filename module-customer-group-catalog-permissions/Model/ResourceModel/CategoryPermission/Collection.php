<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\AbstractCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission as CategoryPermissionResource;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission as CategoryPermissionModel;

/**
 * Class Collection
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = CategoryPermissionInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CategoryPermissionModel::class, CategoryPermissionResource::class);
    }
}
