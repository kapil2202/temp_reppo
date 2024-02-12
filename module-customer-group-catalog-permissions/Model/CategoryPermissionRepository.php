<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\CategoryPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission as CategoryPermissionModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission as CategoryPermissionResourceModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission\Collection
    as CategoryPermissionCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission\CollectionFactory
    as CategoryPermissionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CategoryPermissionRepository
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class CategoryPermissionRepository implements CategoryPermissionRepositoryInterface
{
    /**
     * @var CategoryPermissionResourceModel
     */
    private $resource;

    /**
     * @var CategoryPermissionInterfaceFactory
     */
    private $categoryPermissionInterfaceFactory;

    /**
     * @var CategoryPermissionCollectionFactory
     */
    private $categoryPermissionCollectionFactory;

    /**
     * @var CategoryPermissionSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $permissions = [];

    /**
     * @param CategoryPermissionResourceModel $resource
     * @param CategoryPermissionInterfaceFactory $categoryPermissionInterfaceFactory
     * @param CategoryPermissionCollectionFactory $categoryPermissionCollectionFactory
     * @param CategoryPermissionSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryPermissionResourceModel $resource,
        CategoryPermissionInterfaceFactory $categoryPermissionInterfaceFactory,
        CategoryPermissionCollectionFactory $categoryPermissionCollectionFactory,
        CategoryPermissionSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->categoryPermissionInterfaceFactory = $categoryPermissionInterfaceFactory;
        $this->categoryPermissionCollectionFactory = $categoryPermissionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryPermissionInterface $permission)
    {
        try {
            $this->resource->save($permission);
            $this->permissions[$permission->getPermissionId()] = $permission;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $permission;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryPermissionInterface $permission)
    {
        try {
            $this->resource->delete($permission);
            unset($this->permissions[$permission->getPermissionId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteForCategories($categoryIds)
    {
        $this->resource->deleteForCategories($categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($permissionId)
    {
        if (!isset($this->permissions[$permissionId])) {
            /** @var CategoryPermissionInterface $permission */
            $permission = $this->categoryPermissionInterfaceFactory->create();
            $this->resource->load($permission, $permissionId);
            if (!$permission->getPermissionId()) {
                throw NoSuchEntityException::singleField(CategoryPermissionInterface::ID, $permissionId);
            }
            $this->permissions[$permissionId] = $permission;
        }
        return $this->permissions[$permissionId];
    }

    /**
     * {@inheritdoc}
     */
    public function getForCategory($categoryId, $parentIds, $groupId, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $permissionId = $this->resource->getPermissionIdForCategory($categoryId, $parentIds, $groupId, $storeId);

        return $permissionId ? $this->getById($permissionId) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CategoryPermissionCollection $collection */
        $collection = $this->categoryPermissionCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CategoryPermissionInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CategoryPermissionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var CategoryPermissionModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param CategoryPermissionModel $model
     * @return CategoryPermissionInterface
     */
    private function getDataObject($model)
    {
        /** @var CategoryPermissionInterface $object */
        $object = $this->categoryPermissionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            CategoryPermissionInterface::class
        );
        return $object;
    }
}
