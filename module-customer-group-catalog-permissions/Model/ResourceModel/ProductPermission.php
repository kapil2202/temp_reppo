<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;

/**
 * Class ProductPermission
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel
 */
class ProductPermission extends AbstractResource
{
    /**#@+
     * Constants defined for table
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_cp_product_permissions';
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
     * Get permission id by product id
     *
     * @param int $productId
     * @param int $groupId
     * @param int $storeId
     * @return string
     */
    public function getPermissionIdByProductId($productId, $groupId, $storeId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::MAIN_TABLE_NAME), ProductPermissionInterface::ID)
            ->where(ProductPermissionInterface::PRODUCT_ID . ' = ' . $productId)
            ->where(ProductPermissionInterface::CUSTOMER_GROUP_ID . ' = ' . $groupId . ' '
                . ' OR ' . ProductPermissionInterface::CUSTOMER_GROUP_ID . ' IS NULL')
            ->where(ProductPermissionInterface::STORE_ID . ' = ' . $storeId . ' '
                . ' OR ' . ProductPermissionInterface::STORE_ID . ' IS NULL');

        return $this->getConnection()->fetchOne($select);
    }
    
    /**
     * Delete permissions for products by ids
     *
     * @param array $productIds
     */
    public function deleteForProducts($productIds)
    {
        $productIds = implode(',', $productIds);
        $this->getConnection()->delete(
            $this->getTable(self::MAIN_TABLE_NAME),
            [ProductPermissionInterface::PRODUCT_ID . ' IN (' . $productIds . ')']
        );
    }
    
    /**
     * Get products filter for search collection
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $defaultViewMode
     * @return array
     */
    public function getProductsFilterForSearch($storeId, $customerGroupId, $defaultViewMode)
    {
        $productsSelect = $this->getProductsSelect($storeId, $customerGroupId);
        $fieldName = $defaultViewMode == AccessMode::SHOW ? Applier::NIN_ENTITY_ID : Applier::IN_ENTITY_ID;
        $viewModeValue = $defaultViewMode == AccessMode::SHOW ? AccessMode::HIDE : AccessMode::SHOW;

        $productsSelect->where(ProductPermissionInterface::VIEW_MODE . ' = ?', $viewModeValue);
        $productIds = $this->getConnection()->fetchCol($productsSelect);
        if (empty($productIds) && $viewModeValue == AccessMode::SHOW) {
            $productIds = [0];
        }
        
        return [$fieldName, $productIds];
    }
    
    /**
     * Get products filter
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $defaultViewMode
     * @param bool $isProductChildren
     * @return array
     */
    public function getProductsFilter($storeId, $customerGroupId, $defaultViewMode, $isProductChildren = false)
    {
        $productsSelect = $this->getProductsSelect($storeId, $customerGroupId);
        $conditionType = $defaultViewMode == AccessMode::SHOW ? 'nin' : 'in';
        $viewModeValue = $defaultViewMode == AccessMode::SHOW ? AccessMode::HIDE : AccessMode::SHOW;

        $where = ProductPermissionInterface::VIEW_MODE . ' = ' . $viewModeValue;
        if ($isProductChildren) {
            $where = $where
                . ' OR ' . ProductPermissionInterface::PRICE_MODE . ' = ' . $viewModeValue
                . ' OR ' . ProductPermissionInterface::CHECKOUT_MODE . ' = ' . $viewModeValue;
        }
        $productsSelect->where($where);

        return [$conditionType => $productsSelect];
    }

    /**
     * Get products query
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return \Magento\Framework\DB\Select
     */
    private function getProductsSelect($storeId, $customerGroupId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::MAIN_TABLE_NAME), ProductPermissionInterface::PRODUCT_ID)
            ->where(ProductPermissionInterface::CUSTOMER_GROUP_ID . ' = ' . $customerGroupId . ' '
                . ' OR ' . ProductPermissionInterface::CUSTOMER_GROUP_ID . ' IS NULL')
            ->where(ProductPermissionInterface::STORE_ID . ' = ' . $storeId . ' '
                . ' OR ' . ProductPermissionInterface::STORE_ID . ' IS NULL');

        return $select;
    }
}
