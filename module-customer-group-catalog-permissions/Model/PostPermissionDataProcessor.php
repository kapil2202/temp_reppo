<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\Customer\Groups as GroupSource;

/**
 * Class PostPermissionDataProcessor
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class PostPermissionDataProcessor
{
    /**
     * Use config
     */
    const USE_CONFIG = 'use_config';

    /**
     * Prepare data
     *
     * @param array $permissionsData
     * @return array
     */
    public function prepareData(array $permissionsData)
    {
        foreach ($permissionsData as &$permission) {
            if (isset($permission[AbstractPermissionConverter::STORE_IDS])
                && in_array(0, $permission[AbstractPermissionConverter::STORE_IDS])) {
                $permission[AbstractPermissionConverter::STORE_IDS] = [null];
            }
            if (isset($permission[AbstractPermissionConverter::CUSTOMER_GROUP_IDS])
                && in_array(GroupSource::ALL_GROUPS, $permission[AbstractPermissionConverter::CUSTOMER_GROUP_IDS])) {
                $permission[AbstractPermissionConverter::CUSTOMER_GROUP_IDS] = [null];
            }

            $this->unsetMessages($permission, CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE);
            $this->unsetMessages($permission, CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE);

            if ($this->isNeedResetToConfigValue($permission, CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE)) {
                unset($permission[CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE]);
            }

            if ($this->isNeedResetToConfigValue($permission, CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE)) {
                unset($permission[CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE]);
            }
        }

        return $permissionsData;
    }

    /**
     * @param array $permission
     * @param string $dataKey
     */
    public function unsetMessages(&$permission, $dataKey) {
        if(isset($permission[$dataKey]) && !$permission[$dataKey]) {
            unset($permission[$dataKey]);
        }
    }

    /**
     * Is need reset to config value
     *
     * @param array $permission
     * @param string $dataKey
     * @return bool
     */
    private function isNeedResetToConfigValue($permission, $dataKey)
    {
        return isset($permission[self::USE_CONFIG])
            && isset($permission[self::USE_CONFIG][$dataKey])
            && (bool)$permission[self::USE_CONFIG][$dataKey];
    }
}
