<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Class CategoryPermission
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel
 */
class CategoryPermission extends AbstractResource
{
    /**#@+
     * Constants defined for table
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_cp_category_permissions';
    const MAIN_TABLE_ID_FIELD_NAME = 'permission_id';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Get permission id by category id
     *
     * @param int $categoryId
     * @param array $parentIds
     * @param int $groupId
     * @param int $storeId
     * @return string
     */
    public function getPermissionIdForCategory($categoryId, $parentIds, $groupId, $storeId)
    {
        $categoryIds = array_values($parentIds);
        array_push($categoryIds, $categoryId);

        $select = $this->getConnection()->select()
            ->from(
                ['main_table' => $this->getTable(self::MAIN_TABLE_NAME)],
                CategoryPermissionInterface::ID
            )
            ->joinInner(
                ['category_table' => $this->getTable('catalog_category_entity')],
                'category_table.entity_id = main_table.category_id',
                []
            )
            ->where('main_table.' . CategoryPermissionInterface::CATEGORY_ID . ' IN (?) ', $categoryIds)
            ->where('main_table.' . CategoryPermissionInterface::CUSTOMER_GROUP_ID . ' = ' . $groupId . ' '
                . ' OR main_table.' . CategoryPermissionInterface::CUSTOMER_GROUP_ID . ' IS NULL')
            ->where('main_table.' . CategoryPermissionInterface::STORE_ID . ' = ' . $storeId . ' '
                . ' OR main_table.' . CategoryPermissionInterface::STORE_ID . ' IS NULL')
            ->order('category_table.'  . CategoryInterface::KEY_LEVEL . ' ' . \Magento\Framework\DB\Select::SQL_DESC);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Delete permissions for categories by ids
     *
     * @param array $categoryIds
     */
    public function deleteForCategories($categoryIds)
    {
        $categoryIds = implode(',', $categoryIds);
        $this->getConnection()->delete(
            $this->getTable(self::MAIN_TABLE_NAME),
            [CategoryPermissionInterface::CATEGORY_ID . ' IN (' . $categoryIds . ')']
        );
    }

    /**
     * Get categories filter
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $defaultViewMode
     * @return array
     */
    public function getCategoriesFilter($storeId, $customerGroupId, $defaultViewMode)
    {
        $categoriesSelect = $this->getCategoriesSelect($storeId, $customerGroupId);
        $conditionType = $defaultViewMode == AccessMode::SHOW ? 'nin' : 'in';
        $viewModeValue = $defaultViewMode == AccessMode::SHOW ? AccessMode::HIDE : AccessMode::SHOW;

        $categoriesSelect->where($this->getTable(self::MAIN_TABLE_NAME) . '.' .
            CategoryPermissionInterface::VIEW_MODE . ' = ?', $viewModeValue);

        return [$conditionType => $categoriesSelect];
    }

    /**
     * Get children categories filter
     *
     * @param array $childrenIds
     * @return array
     */
    public function getChildrenCategoriesFilter(array $childrenIds): array
    {
        $mainTable = $this->getTable(self::MAIN_TABLE_NAME);
        $categoriesWithPermissionsSelect = $this->getConnection()->select()
            ->from($mainTable, CategoryPermissionInterface::CATEGORY_ID)
            ->where($mainTable . '.' . CategoryPermissionInterface::CATEGORY_ID . ' IN (?) ', $childrenIds);
        $categoriesWithPermissions = $this->getConnection()->fetchCol($categoriesWithPermissionsSelect);
        $categoriesWithoutPermissions = array_diff($childrenIds, $categoriesWithPermissions);

        return $categoriesWithoutPermissions ? ['in' => $categoriesWithoutPermissions] : [];
    }

    /**
     * Get categories query
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return \Magento\Framework\DB\Select
     */
    private function getCategoriesSelect($storeId, $customerGroupId)
    {
        $mainTable = $this->getTable(self::MAIN_TABLE_NAME);
        return $this->getConnection()->select()
            ->from($mainTable, CategoryPermissionInterface::CATEGORY_ID)
            ->where($mainTable . '.' . CategoryPermissionInterface::CUSTOMER_GROUP_ID . ' = ' . $customerGroupId .
                ' ' . ' OR ' . $mainTable . '.' . CategoryPermissionInterface::CUSTOMER_GROUP_ID . ' IS NULL')
            ->where($mainTable . '.' . CategoryPermissionInterface::STORE_ID . ' = ' . $storeId . ' '
                . ' OR ' . $mainTable . '.' . CategoryPermissionInterface::STORE_ID . ' IS NULL');
    }
}
