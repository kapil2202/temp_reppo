<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Cms\Model;

use Magento\Cms\Api\Data\PageInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermissionManager;
use Magento\Cms\Api\PageRepositoryInterface;

/**
 * Class PageRepositoryPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Cms\Model
 */
class PageRepositoryPlugin
{
    /**
     * Permissions changed
     */
    const PERMISSIONS_CHANGED = 'permissions_changed';

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
     * Save permissions for CMS page after save
     *
     * @param PageRepositoryInterface $subject
     * @param PageInterface $page
     * @return PageInterface
     * @throws \Exception
     */
    public function afterSave(PageRepositoryInterface $subject, PageInterface $page)
    {
        $pageId = $page->getId();
        $data = $page->getData();

        if ($this->isNeedToSavePermission($data)) {
            $permissionsData = $this->getPermissionsData($data);
            if ($permissionsData) {
                $this->permissionManager->prepareAndSavePermissions($permissionsData, $pageId);
            }
        }

        return $page;
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
     * Retrieve permissions data from CMS page data
     *
     * @param array $data
     * @return array
     */
    private function getPermissionsData(array $data)
    {
        return isset($data[CmsPagePermissionManager::CMS_PAGE_PERMISSIONS_KEY])
            ? $data[CmsPagePermissionManager::CMS_PAGE_PERMISSIONS_KEY]
            : [];
    }
}
