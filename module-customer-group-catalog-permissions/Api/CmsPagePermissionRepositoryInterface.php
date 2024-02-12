<?php
namespace Aheadworks\CustGroupCatPermissions\Api;

/**
 * Interface CmsPagePermissionRepositoryInterface
 * @package Aheadworks\CustGroupCatPermissions\Api
 */
interface CmsPagePermissionRepositoryInterface
{
    /**
     * Save permission
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface $permission
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface $permission
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface $permission);

    /**
     * Delete permission
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface $permission
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface $permission);

    /**
     * Delete permission for CMS pages by ids
     *
     * @param array $pageIds
     */
    public function deleteForPages($pageIds);

    /**
     * Retrieve permission by id
     *
     * @param int $permissionId
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($permissionId);

    /**
     * Retrieve permission for CMS page
     *
     * @param int $pageId
     * @param int $groupId
     * @param int|null $storeId
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForCmsPage($pageId, $groupId, $storeId = null);

    /**
     * Retrieve permissions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
