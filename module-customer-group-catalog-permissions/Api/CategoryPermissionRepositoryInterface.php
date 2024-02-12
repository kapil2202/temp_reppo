<?php
namespace Aheadworks\CustGroupCatPermissions\Api;

/**
 * Interface CategoryPermissionRepositoryInterface
 * @package Aheadworks\CustGroupCatPermissions\Api
 */
interface CategoryPermissionRepositoryInterface
{
    /**
     * Save permission
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface $permission
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface $permission
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface $permission);

    /**
     * Delete permission
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface $permission
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface $permission);

    /**
     * Delete permission by category id
     *
     * @param array $categoryIds
     */
    public function deleteForCategories($categoryIds);

    /**
     * Retrieve permission by id
     *
     * @param int $permissionId
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($permissionId);

    /**
     * Retrieve permission for category
     *
     * @param int $categoryId
     * @param array $parentIds
     * @param int $groupId
     * @param int|null $storeId
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForCategory($categoryId, $parentIds, $groupId, $storeId = null);

    /**
     * Retrieve permissions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
