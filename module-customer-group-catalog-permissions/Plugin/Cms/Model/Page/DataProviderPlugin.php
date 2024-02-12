<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Cms\Model\Page;

use Magento\Cms\Model\Page\DataProvider as CmsPageDataProvider;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermissionManager;

/**
 * Class DataProviderPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Cms\Model\Page
 */
class DataProviderPlugin
{
    /**
     * @var CmsPagePermissionManager
     */
    private $permissionManager;

    /**
     * @param CmsPagePermissionManager $processor
     */
    public function __construct(
        CmsPagePermissionManager $processor
    ) {
        $this->permissionManager = $processor;
    }

    /**
     * Modify data
     *
     * @param CmsPageDataProvider $subject
     * @param array $data
     * @return array
     */
    public function afterGetData(CmsPageDataProvider $subject, $data)
    {
        if (is_array($data)) {
            foreach ($data as $pageId => &$pageData) {
                $permissions = $this->permissionManager->prepareAndLoadPermissions($pageId);
                if (!empty($permissions)) {
                    $pageData[CmsPagePermissionManager::CMS_PAGE_PERMISSIONS_KEY] = $permissions;
                }
            }
        }
        return $data;
    }
}
