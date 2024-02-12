<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\AbstractCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission as CmsPagePermissionResource;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission as CmsPagePermissionModel;

/**
 * Class Collection
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = CmsPagePermissionInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CmsPagePermissionModel::class, CmsPagePermissionResource::class);
    }
}
