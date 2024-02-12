<?php
namespace Aheadworks\CustGroupCatPermissions\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CmsPagePermissionInterface
 * @package Aheadworks\CustGroupCatPermissions\Api\Data
 */
interface CmsPagePermissionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'permission_id';
    const CMS_PAGE_ID = 'cms_page_id';
    const STORE_ID = 'store_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const VIEW_MODE = 'view_mode';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
     */
    public function getPermissionId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setPermissionId($id);

    /**
     * Get CMS page id
     *
     * @return int
     */
    public function getCmsPageId();

    /**
     * Set CMS page id
     *
     * @param int $cmsPageId
     * @return $this
     */
    public function setCmsPageId($cmsPageId);

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get customer group id
     *
     * @return int
     */
    public function getCustomerGroupId();

    /**
     * Set customer group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setCustomerGroupId($groupId);

    /**
     * Get view mode
     *
     * @return int
     */
    public function getViewMode();

    /**
     * Set view mode
     *
     * @param int $mode
     * @return $this
     */
    public function setViewMode($mode);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionExtensionInterface $extensionAttributes
    );
}
