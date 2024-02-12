<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\Customer\Groups as GroupSource;
use Magento\Framework\DataObject;

/**
 * Class AbstractPermissionConverter
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
abstract class AbstractPermissionConverter
{
    /**
     * @var string
     */
    protected $relatedObjectKey;

    /**#@+
     * Constants defined for keys of the data array.
     */
    const STORE_IDS = 'store_ids';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    /**#@-*/

    /**
     * Convert permissions array to objects
     *
     * @param array $permissions
     * @param int $objectId
     * @return array
     */
    public function convertPermissionsToSave(array $permissions, $objectId)
    {
        $objects = [];
        foreach ($permissions as $permissionData) {
            $objects[] = $this->prepareObjects($permissionData, $objectId);
        }

        return array_merge([], ...$objects);
    }

    /**
     * Convert permission objects to array
     *
     * @param array $permissions
     * @return array
     */
    public function convertPermissionsToDisplay($permissions)
    {
        $permissionsArray = [];
        foreach ($permissions as $permission) {
            $permissionsArray[] = $permission->toArray();
        }

        return $this->preparePermissionsArrayToDisplay($permissionsArray);
    }

    /**
     * Prepare permission objects
     *
     * @param array $permissionData
     * @param int $objectId
     * @return array
     */
    protected function prepareObjects(array $permissionData, $objectId)
    {
        $objects = [];
        $permissionObject = new DataObject($permissionData);
        $recordId = $permissionObject->getRecordId();
        $storeIds = $permissionObject->getStoreIds();
        $customerGroupIds = $permissionObject->getCustomerGroupIds();
        $viewMode = $permissionObject->getViewMode();
        $priceMode = $permissionObject->getPriceMode();
        $checkoutMode = $permissionObject->getCheckoutMode();
        $hiddenPriceMessage = $permissionObject->getHiddenPriceMessage();
        $hiddenAddToCartMessage = $permissionObject->getHiddenAddToCartMessage();

        foreach ($storeIds as $storeId) {
            foreach ($customerGroupIds as $groupId) {
                $permission = [
                    $this->relatedObjectKey => $objectId,
                    CategoryPermissionInterface::RECORD_ID => $recordId,
                    CategoryPermissionInterface::STORE_ID => $storeId,
                    CategoryPermissionInterface::CUSTOMER_GROUP_ID => $groupId,
                    CategoryPermissionInterface::VIEW_MODE => $viewMode,
                    CategoryPermissionInterface::PRICE_MODE => $priceMode,
                    CategoryPermissionInterface::CHECKOUT_MODE => $checkoutMode,
                    CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => $hiddenPriceMessage,
                    CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => $hiddenAddToCartMessage
                ];
                $objects[] = $this->getDataObject($permission);
            }
        }

        return $objects;
    }

    /**
     * Prepare permissions array to display
     *
     * @param array $permissionsArray
     * @return array
     */
    protected function preparePermissionsArrayToDisplay($permissionsArray)
    {
        $prepared = [];

        foreach ($permissionsArray as $permission) {
            $linkageKey = $this->getLinkageKey($permission);
            $storeId = $permission[CategoryPermissionInterface::STORE_ID] !== null
                ? $permission[CategoryPermissionInterface::STORE_ID]
                : 0;
            $groupId = $permission[CategoryPermissionInterface::CUSTOMER_GROUP_ID] !== null
                ? $permission[CategoryPermissionInterface::CUSTOMER_GROUP_ID]
                : GroupSource::ALL_GROUPS;

            if (array_key_exists($linkageKey, $prepared)) {
                $mergedStoreIds = $prepared[$linkageKey][self::STORE_IDS];
                array_push($mergedStoreIds, $storeId);
                $mergedCustomerGroupIds = $prepared[$linkageKey][self::CUSTOMER_GROUP_IDS];
                array_push($mergedCustomerGroupIds, $groupId);
                $prepared[$linkageKey][self::STORE_IDS] = array_values(array_unique($mergedStoreIds));
                $prepared[$linkageKey][self::CUSTOMER_GROUP_IDS] = array_values(array_unique($mergedCustomerGroupIds));
            } else {
                unset($permission[CategoryPermissionInterface::STORE_ID]);
                unset($permission[CategoryPermissionInterface::CUSTOMER_GROUP_ID]);
                $permission[self::STORE_IDS] = [$storeId];
                $permission[self::CUSTOMER_GROUP_IDS] = [$groupId];
                $prepared[$linkageKey] = $permission;
            }
        }

        return array_values($prepared);
    }

    /**
     * Get linkage key
     *
     * @param array $permission
     * @return string
     */
    protected function getLinkageKey(array $permission)
    {
        return $permission[CategoryPermissionInterface::RECORD_ID];
    }

    /**
     * Convert permissions data to object
     *
     * @param $permissionData
     */
    abstract public function getDataObject(array $permissionData);
}
