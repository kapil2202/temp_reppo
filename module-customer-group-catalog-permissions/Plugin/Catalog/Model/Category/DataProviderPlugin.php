<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Category;

use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermissionManager;

/**
 * Class DataProviderPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Category
 */
class DataProviderPlugin
{
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
     * Modify data
     *
     * @param CategoryDataProvider $subject
     * @param array $data
     * @return array
     */
    public function afterGetData(CategoryDataProvider $subject, $data)
    {
        if (is_array($data)) {
            foreach ($data as $categoryId => &$categoryData) {
                $categoryData[CategoryPermissionManager::CATEGORY_PERMISSIONS_KEY] =
                    $this->permissionManager->prepareAndLoadPermissions($categoryId);
            }
        }
        return $data;
    }
}
