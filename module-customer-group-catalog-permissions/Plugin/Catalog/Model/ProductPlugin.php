<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model;

use Magento\Catalog\Model\Product as ProductModel;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermissionManager;

/**
 * Class ProductPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model
 */
class ProductPlugin
{
    /**
     * Permissions changed
     */
    const PERMISSIONS_CHANGED = 'permissions_changed';

    /**
     * @var ProductPermissionManager
     */
    private $permissionManager;

    /**
     * @param ProductPermissionManager $processor
     */
    public function __construct(
        ProductPermissionManager $processor
    ) {
        $this->permissionManager = $processor;
    }

    /**
     * Save product permissions on product after save
     *
     * @param ProductModel $subject
     * @return ProductModel
     * @throws \Exception
     */
    public function afterSave(ProductModel $subject)
    {
        $productId = $subject->getId();
        $data = $subject->getData();

        if ($this->isNeedToSavePermission($data)) {
            $permissionsData = $this->getPermissionsData($data);
            $this->permissionManager->prepareAndSavePermissions($permissionsData, $productId);
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
     * Retrieve permissions data from product data
     *
     * @param array $data
     * @return array
     */
    private function getPermissionsData(array $data)
    {
        return isset($data[ProductPermissionManager::PRODUCT_PERMISSIONS_KEY])
            ? $data[ProductPermissionManager::PRODUCT_PERMISSIONS_KEY]
            : [];
    }
}
