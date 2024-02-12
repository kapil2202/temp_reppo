<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model;

use Magento\Catalog\Model\Category as CategoryModel;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermissionManager;

/**
 * Class CategoryPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model
 */
class CategoryPlugin
{
    /**
     * Permissions changed
     */
    const PERMISSIONS_CHANGED = 'permissions_changed';

    /**
     * @var CategoryPermissionManager
     */
    private $permissionManager;

    /**
     * @param CategoryPermissionManager $processor
     */
    public function __construct(
        CategoryPermissionManager $processor
    ) {
        $this->permissionManager = $processor;
    }

    /**
     * Save category permissions on category after save
     *
     * @param CategoryModel $subject
     * @return CategoryModel
     * @throws \Exception
     */
    public function afterSave(CategoryModel $subject)
    {
        $categoryId = $subject->getId();
        $data = $subject->getData();

        if ($this->isNeedToSavePermission($data)) {
            $permissionsData = $this->getPermissionsData($data);
            $this->permissionManager->prepareAndSavePermissions($permissionsData, $categoryId);
        }

        return $subject;
    }

    /**
     * Check is need to save permissions
     *
     * @param array $data
     * @return bool
     */
    private function isNeedToSavePermission($data)
    {
        return isset($data[self::PERMISSIONS_CHANGED]) && (int)$data[self::PERMISSIONS_CHANGED];
    }

    /**
     * Retrieve permissions data from category data
     *
     * @param array $data
     * @return array
     */
    private function getPermissionsData(array $data)
    {
        return isset($data[CategoryPermissionManager::CATEGORY_PERMISSIONS_KEY])
            ? $data[CategoryPermissionManager::CATEGORY_PERMISSIONS_KEY]
            : [];
    }
}
