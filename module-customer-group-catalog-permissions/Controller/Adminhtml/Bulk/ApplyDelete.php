<?php
namespace Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk;

use Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk;

/**
 * Class ApplyDelete
 * @package Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk
 */
class ApplyDelete extends Bulk
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_CustGroupCatPermissions::bulk_delete';

    /**
     * Delete permissions for the specified entities
     *
     * {@inheritdoc}
     */
    protected function applyAction($data)
    {
        $entityType = $this->getEntityType($data);
        $entityIds = $this->getEntityIds($data);
        $permissionManager = $this->getPermissionManagerByType($entityType);

        if ($permissionManager) {
            $permissionManager->deleteOldPermissions($entityIds);
        } else {
            throw new \Exception(
                (string)__('Invalid entity type "%1"', $entityType)
            );
        }

        return [
            'message' => __('Permissions have been removed successfully.')
        ];
    }
}
