<?php
namespace Aheadworks\CustGroupCatPermissions\Api;

/**
 * Interface ProductPermissionRepositoryInterface
 * @package Aheadworks\CustGroupCatPermissions\Api
 */
interface ProductPermissionRepositoryInterface
{
    /**
     * Save permission
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface $permission
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface $permission
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface $permission);

    /**
     * Delete permission
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface $permission
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface $permission);

    /**
     * Delete permission for products by ids
     *
     * @param array $productIds
     */
    public function deleteForProducts($productIds);

    /**
     * Retrieve permission by id
     *
     * @param int $permissionId
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($permissionId);

    /**
     * Retrieve permission for product
     *
     * @param int $productId
     * @param int $groupId
     * @param int|null $storeId
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForProduct($productId, $groupId, $storeId = null);

    /**
     * Retrieve permissions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
