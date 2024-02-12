<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\ProductPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission as ProductPermissionModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission as ProductPermissionResourceModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission\Collection
    as ProductPermissionCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission\CollectionFactory
    as ProductPermissionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductPermissionRepository
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class ProductPermissionRepository implements ProductPermissionRepositoryInterface
{
    /**
     * @var ProductPermissionResourceModel
     */
    private $resource;

    /**
     * @var ProductPermissionInterfaceFactory
     */
    private $productPermissionInterfaceFactory;

    /**
     * @var ProductPermissionCollectionFactory
     */
    private $productPermissionCollectionFactory;

    /**
     * @var ProductPermissionSearchResultsInterfaceFactory
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
     * @param ProductPermissionResourceModel $resource
     * @param ProductPermissionInterfaceFactory $productPermissionInterfaceFactory
     * @param ProductPermissionCollectionFactory $productPermissionCollectionFactory
     * @param ProductPermissionSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductPermissionResourceModel $resource,
        ProductPermissionInterfaceFactory $productPermissionInterfaceFactory,
        ProductPermissionCollectionFactory $productPermissionCollectionFactory,
        ProductPermissionSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->productPermissionInterfaceFactory = $productPermissionInterfaceFactory;
        $this->productPermissionCollectionFactory = $productPermissionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ProductPermissionInterface $permission)
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
    public function delete(ProductPermissionInterface $permission)
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
    public function deleteForProducts($productIds)
    {
        $this->resource->deleteForProducts($productIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($permissionId)
    {
        if (!isset($this->permissions[$permissionId])) {
            /** @var ProductPermissionInterface $permission */
            $permission = $this->productPermissionInterfaceFactory->create();
            $this->resource->load($permission, $permissionId);
            if (!$permission->getPermissionId()) {
                throw NoSuchEntityException::singleField(ProductPermissionInterface::ID, $permissionId);
            }
            $this->permissions[$permissionId] = $permission;
        }
        return $this->permissions[$permissionId];
    }

    /**
     * {@inheritdoc}
     */
    public function getForProduct($productId, $groupId, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $permissionId = $this->resource->getPermissionIdByProductId($productId, $groupId, $storeId);

        return $permissionId ? $this->getById($permissionId) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ProductPermissionCollection $collection */
        $collection = $this->productPermissionCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, ProductPermissionInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProductPermissionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var ProductPermissionModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param ProductPermissionModel $model
     * @return ProductPermissionInterface
     */
    private function getDataObject($model)
    {
        /** @var ProductPermissionInterface $object */
        $object = $this->productPermissionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            ProductPermissionInterface::class
        );
        return $object;
    }
}
