<?php
namespace Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission as PermissionResource;
use Magento\Cms\Model\Page;

/**
 * Class Applier
 * @package Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission
 */
class Applier
{
    /**#@+
     * Constants defined for keys of the data array.
     */
    const PERMISSION_APPLIED = 'permission_applied';
    const HIDE_PAGE = 'hide_page';
    /**#@-*/

    /**
     * @var Resolver
     */
    private $permissionResolver;

    /**
     * @var PermissionResource
     */
    private $resource;

    /**
     * @param Resolver $resolver
     * @param PermissionResource $resource
     */
    public function __construct(
        Resolver $resolver,
        PermissionResource $resource
    ) {
        $this->permissionResolver = $resolver;
        $this->resource = $resource;
    }

    /**
     * Apply permission for CMS page
     *
     * @param Page $cmsPage
     */
    public function applyForCmsPage($cmsPage)
    {
        if (!$cmsPage->getData(self::PERMISSION_APPLIED)) {
            /** @var CmsPagePermissionInterface $permission */
            $permission = $this->permissionResolver->getPermissionForCmsPage($cmsPage);
            if ($permission) {
                if ($permission->getViewMode() == AccessMode::HIDE) {
                    $cmsPage->setData(self::HIDE_PAGE, true);
                }
                $cmsPage->setData(self::PERMISSION_APPLIED, true);
            }
        }
    }
}
