<?php
namespace Aheadworks\CustGroupCatPermissions\Api;

/**
 * Interface PermissionManagerInterface
 * @package Aheadworks\CustGroupCatPermissions\Api
 */
interface PermissionManagerInterface
{
    /**
     * Prepare and save permissions
     *
     * @param array $permissionsData
     * @param int $objectId
     * @throws \Exception
     */
    public function prepareAndSavePermissions(array $permissionsData, $objectId);

    /**
     * Prepare and load permissions
     *
     * @param int $objectId
     * @return array
     */
    public function prepareAndLoadPermissions($objectId);

    /**
     * Delete old permissions
     *
     * @param array $objectIds
     */
    public function deleteOldPermissions(array $objectIds);

    /**
     * Retrieve permissions key of the data array
     *
     * @return string
     */
    public function getPermissionsDataKey();
}
