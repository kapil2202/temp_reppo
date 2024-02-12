<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\ProductPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Converter as PermissionsConverter;
use Aheadworks\CustGroupCatPermissions\Api\PermissionManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class ProductPermissionManager
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class ProductPermissionManager implements PermissionManagerInterface
{
    /**
     * Constant defined for keys of the data array.
     */
    const PRODUCT_PERMISSIONS_KEY = 'aw_cgcp_product_permissions';

    /**
     * @var ProductPermissionRepositoryInterface
     */
    private $productPermissionRepository;

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
     * @param ProductPermissionRepositoryInterface $productPermissionRepository
     * @param PermissionsConverter $permissionsConverter
     * @param PostPermissionDataProcessor $postPermissionDataProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductPermissionRepositoryInterface $productPermissionRepository,
        PermissionsConverter $permissionsConverter,
        PostPermissionDataProcessor $postPermissionDataProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productPermissionRepository = $productPermissionRepository;
        $this->permissionsConverter = $permissionsConverter;
        $this->postPermissionDataProcessor = $postPermissionDataProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareAndSavePermissions(array $permissionsData, $productId)
    {
        $permissionsData = $this->postPermissionDataProcessor->prepareData($permissionsData);
        $permissions = $this->permissionsConverter->convertPermissionsToSave($permissionsData, $productId);
        $this->deleteOldPermissions([$productId]);
        foreach ($permissions as $permission) {
            try {
                $this->productPermissionRepository->save($permission);
            } catch (CouldNotSaveException $e) {
                throw new \Exception((string)__('Something went wrong while saving permissions.'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareAndLoadPermissions($productId)
    {
        $preparedPermissions = [];
        $this->searchCriteriaBuilder->addFilter(ProductPermissionInterface::PRODUCT_ID, $productId);
        $searchResult = $this->productPermissionRepository->getList($this->searchCriteriaBuilder->create());
        $permissions = $searchResult->getItems();

        if ($permissions) {
            $preparedPermissions = $this->permissionsConverter->convertPermissionsToDisplay($permissions);
        }

        return $preparedPermissions;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOldPermissions(array $productIds)
    {
        $this->productPermissionRepository->deleteForProducts($productIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionsDataKey()
    {
        return self::PRODUCT_PERMISSIONS_KEY;
    }
}
