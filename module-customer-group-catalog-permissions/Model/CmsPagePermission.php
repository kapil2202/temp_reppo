<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission as PermissionResource;

/**
 * Class CmsPagePermission
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class CmsPagePermission extends AbstractModel implements CmsPagePermissionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(PermissionResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermissionId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCmsPageId()
    {
        return $this->getData(self::CMS_PAGE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCmsPageId($cmsPageId)
    {
        return $this->setData(self::CMS_PAGE_ID, $cmsPageId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupId($groupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $groupId);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewMode()
    {
        return $this->getData(self::VIEW_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewMode($mode)
    {
        return $this->setData(self::VIEW_MODE, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
