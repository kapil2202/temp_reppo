<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\CategoryPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Converter as PermissionsConverter;
use Aheadworks\CustGroupCatPermissions\Api\PermissionManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CategoryPermissionManager
 * @package Aheadworks\CustGroupCatPermissions\Model\CategoryPermission
 */
class CategoryPermissionManager implements PermissionManagerInterface
{
    /**
     * Constant defined for keys of the data array.
     */
    const CATEGORY_PERMISSIONS_KEY = 'aw_cgcp_category_permissions';

    /**
     * @var CategoryPermissionRepositoryInterface
     */
    private $categoryPermissionRepository;

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
     * @param CategoryPermissionRepositoryInterface $categoryPermissionRepository
     * @param PermissionsConverter $permissionsConverter
     * @param PostPermissionDataProcessor $postPermissionDataProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CategoryPermissionRepositoryInterface $categoryPermissionRepository,
        PermissionsConverter $permissionsConverter,
        PostPermissionDataProcessor $postPermissionDataProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->categoryPermissionRepository = $categoryPermissionRepository;
        $this->permissionsConverter = $permissionsConverter;
        $this->postPermissionDataProcessor = $postPermissionDataProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareAndSavePermissions(array $permissionsData, $categoryId)
    {
        $permissionsData = $this->postPermissionDataProcessor->prepareData($permissionsData);
        $permissions = $this->permissionsConverter->convertPermissionsToSave($permissionsData, $categoryId);
        $this->deleteOldPermissions([$categoryId]);
        foreach ($permissions as $permission) {
            try {
                $this->categoryPermissionRepository->save($permission);
            } catch (CouldNotSaveException $e) {
                throw new \Exception((string)__('Something went wrong while saving permissions.'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareAndLoadPermissions($categoryId)
    {
        $preparedPermissions = [];
        $this->searchCriteriaBuilder->addFilter(CategoryPermissionInterface::CATEGORY_ID, $categoryId);
        $searchResult = $this->categoryPermissionRepository->getList($this->searchCriteriaBuilder->create());
        $permissions = $searchResult->getItems();

        if ($permissions) {
            $preparedPermissions = $this->permissionsConverter->convertPermissionsToDisplay($permissions);
        }

        return $preparedPermissions;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOldPermissions(array $categoryIds)
    {
        $this->categoryPermissionRepository->deleteForCategories($categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionsDataKey()
    {
        return self::CATEGORY_PERMISSIONS_KEY;
    }
}
