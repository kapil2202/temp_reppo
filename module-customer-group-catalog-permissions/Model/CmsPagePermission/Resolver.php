<?php
namespace Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\CmsPagePermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionResolver;
use Magento\Customer\Model\Session;

/**
 * Class Resolver
 * @package Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission
 */
class Resolver extends AbstractPermissionResolver
{
    /**
     * @var CmsPagePermissionRepositoryInterface
     */
    private $cmsPagePermissionRepository;

    /**
     * @var Converter
     */
    private $permissionConverter;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param Session $session
     * @param CmsPagePermissionRepositoryInterface $cmsPagePermissionRepository
     * @param Converter $converter
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        Session $session,
        CmsPagePermissionRepositoryInterface $cmsPagePermissionRepository,
        Converter $converter
    ) {
        parent::__construct($config, $storeManager, $httpContext, $session);
        $this->cmsPagePermissionRepository = $cmsPagePermissionRepository;
        $this->permissionConverter = $converter;
    }

    /**
     * Get permission for CMS page
     *
     * @param PageInterface $cmsPage
     * @return CmsPagePermissionInterface
     */
    public function getPermissionForCmsPage($cmsPage)
    {
        $cmsPageId = $cmsPage->getId();
        $storeId = $this->getStoreId();
        $customerGroupId = $this->getCustomerGroupId();

        try {
            $result = $this->cmsPagePermissionRepository->getForCmsPage(
                $cmsPageId,
                $customerGroupId,
                $storeId
            );
            if (!$result) {
                $result = $this->getDefaultPermission();
            }
        } catch (NoSuchEntityException $e) {
            $result = $this->getDefaultPermission();
        }

        return $result;
    }

    /**
     * Retrieve default permission
     *
     * @return CmsPagePermissionInterface
     */
    protected function getDefaultPermission()
    {
        $storeId = $this->getStoreId();
        $customerGroupId = $this->getCustomerGroupId();

        $permissionData = [
            CmsPagePermissionInterface::VIEW_MODE => $this->resolveConfigViewMode($storeId, $customerGroupId),
        ];

        return $this->permissionConverter->getDataObject($permissionData);
    }

    /**
     * Resolve view mode value
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return int
     */
    protected function resolveConfigViewMode($storeId, $customerGroupId)
    {
        $viewModeConfigValue = $this->config->getCmsPageBrowsingMode($storeId);
        $customerGroupIds = $this->config->getCmsPageBrowsingCustomerGroups($storeId);

        return $this->resolveConfigModeValue($viewModeConfigValue, $customerGroupId, $customerGroupIds);
    }

    /**
     * {@inheritdoc}
     */
    protected function convertDefaultPermissionToObject($permissionData)
    {
        return $this->permissionConverter->getDataObject($permissionData);
    }
}
