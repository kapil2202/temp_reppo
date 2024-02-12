<?php
namespace Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk;

use Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk;

/**
 * Class ApplyUpdate
 *
 * @package Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk
 */
class ApplyUpdate extends Bulk
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_CustGroupCatPermissions::bulk_update';

    /**
     * Apply configured permissions to the specified entities
     *
     * {@inheritdoc}
     */
    protected function applyAction($data)
    {
        $entityType = $this->getEntityType($data);
        $entityIds = $this->getEntityIds($data);
        $permissionManager = $this->getPermissionManagerByType($entityType);
        if ($permissionManager) {
            $permissionsData = $this->getPermissionsData($data, $permissionManager->getPermissionsDataKey());
            foreach ($entityIds as $entityId) {
                $permissionManager->prepareAndSavePermissions(
                    $permissionsData,
                    $entityId
                );
            }
        } else {
            throw new \Exception(
                (string) __('Invalid entity type "%1"', $entityType)
            );
        }
        return [
            'message' => __('Permissions have been applied successfully.')
        ];
    }

    /**
     * Retrieve permissions data to apply
     *
     * @param array $data
     * @param string $permissionsDataKey
     * @return array
     * @throws \Exception
     */
    private function getPermissionsData(array $data, $permissionsDataKey)
    {
        if (empty($data[$permissionsDataKey])) {
            throw new \Exception(
                (string)__('Some of required data is missing.')
            );
        } else {
            return $data[$permissionsDataKey];
        }
    }
}
