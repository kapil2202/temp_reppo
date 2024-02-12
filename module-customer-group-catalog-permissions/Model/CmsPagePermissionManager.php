<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\CmsPagePermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Converter as PermissionsConverter;
use Aheadworks\CustGroupCatPermissions\Api\PermissionManagerInterface;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CmsPagePermissionManager
 * @package Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission
 */
class CmsPagePermissionManager implements PermissionManagerInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     */
    const CMS_PAGE_PERMISSIONS_KEY = 'aw_cgcp_cms_page_permissions';
    const WEB_CONFIG_URL = 'web_config_url';
    /**#@-*/

    /**
     * @var CmsPagePermissionRepositoryInterface
     */
    private $cmsPagePermissionRepository;

    /**
     * @var PermissionsConverter
     */
    private $permissionsConverter;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PostPermissionDataProcessor
     */
    private $postPermissionDataProcessor;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param CmsPagePermissionRepositoryInterface $cmsPagePermissionRepository
     * @param PermissionsConverter $permissionsConverter
     * @param PostPermissionDataProcessor $postPermissionDataProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UrlInterface $url
     */
    public function __construct(
        CmsPagePermissionRepositoryInterface $cmsPagePermissionRepository,
        PermissionsConverter $permissionsConverter,
        PostPermissionDataProcessor $postPermissionDataProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlInterface $url
    ) {
        $this->cmsPagePermissionRepository = $cmsPagePermissionRepository;
        $this->permissionsConverter = $permissionsConverter;
        $this->postPermissionDataProcessor = $postPermissionDataProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->urlBuilder = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareAndSavePermissions(array $permissionsData, $cmsPageId)
    {
        $preprocessedPermissionsData = $this->preprocessCmsPagePermissionsData($permissionsData);
        $permissionsData = $this->postPermissionDataProcessor->prepareData($preprocessedPermissionsData);
        $permissions = $this->permissionsConverter->convertPermissionsToSave($permissionsData, $cmsPageId);
        $this->deleteOldPermissions([$cmsPageId]);
        foreach ($permissions as $permission) {
            try {
                $this->cmsPagePermissionRepository->save($permission);
            } catch (CouldNotSaveException $e) {
                throw new \Exception((string)__('Something went wrong while saving permissions.'));
            }
        }
    }

    /**
     * Preprocess cms page permissions data to provide compatibility with other permissions, that use dynamic rows
     *
     * @param array $permissionsData
     * @return array
     */
    private function preprocessCmsPagePermissionsData(array $permissionsData)
    {
        return [$permissionsData];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareAndLoadPermissions($cmsPageId)
    {
        $this->searchCriteriaBuilder->addFilter(CmsPagePermissionInterface::CMS_PAGE_ID, $cmsPageId);
        $searchResult = $this->cmsPagePermissionRepository->getList($this->searchCriteriaBuilder->create());
        $permissions = $searchResult->getItems();

        if ($permissions) {
            $preparedPermissions = $this->permissionsConverter->convertPermissionsToDisplay($permissions);
        }
        $preparedPermissions[self::WEB_CONFIG_URL] = $this->getUrlToWebConfig();

        return $preparedPermissions;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOldPermissions(array $cmsPageIds)
    {
        $this->cmsPagePermissionRepository->deleteForPages($cmsPageIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionsDataKey()
    {
        return self::CMS_PAGE_PERMISSIONS_KEY;
    }

    /**
     * Retrieve url to store web config
     *
     * @return string
     */
    private function getUrlToWebConfig()
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit/section/web',
            ['_fragment' => 'web_default-link']
        );
    }
}
