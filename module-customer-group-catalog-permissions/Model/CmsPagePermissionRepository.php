<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\CmsPagePermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission as CmsPagePermissionModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission as CmsPagePermissionResourceModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission\Collection
    as CmsPagePermissionCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission\CollectionFactory
    as CmsPagePermissionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CmsPagePermissionRepository
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class CmsPagePermissionRepository implements CmsPagePermissionRepositoryInterface
{
    /**
     * @var CmsPagePermissionResourceModel
     */
    private $resource;

    /**
     * @var CmsPagePermissionInterfaceFactory
     */
    private $cmsPagePermissionInterfaceFactory;

    /**
     * @var CmsPagePermissionCollectionFactory
     */
    private $cmsPagePermissionCollectionFactory;

    /**
     * @var CmsPagePermissionSearchResultsInterfaceFactory
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
     * @param CmsPagePermissionResourceModel $resource
     * @param CmsPagePermissionInterfaceFactory $cmsPagePermissionInterfaceFactory
     * @param CmsPagePermissionCollectionFactory $cmsPagePermissionCollectionFactory
     * @param CmsPagePermissionSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CmsPagePermissionResourceModel $resource,
        CmsPagePermissionInterfaceFactory $cmsPagePermissionInterfaceFactory,
        CmsPagePermissionCollectionFactory $cmsPagePermissionCollectionFactory,
        CmsPagePermissionSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->cmsPagePermissionInterfaceFactory = $cmsPagePermissionInterfaceFactory;
        $this->cmsPagePermissionCollectionFactory = $cmsPagePermissionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CmsPagePermissionInterface $permission)
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
    public function delete(CmsPagePermissionInterface $permission)
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
    public function deleteForPages($pageIds)
    {
        $this->resource->deleteForPages($pageIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($permissionId)
    {
        if (!isset($this->permissions[$permissionId])) {
            /** @var CmsPagePermissionInterface $permission */
            $permission = $this->cmsPagePermissionInterfaceFactory->create();
            $this->resource->load($permission, $permissionId);
            if (!$permission->getPermissionId()) {
                throw NoSuchEntityException::singleField(CmsPagePermissionInterface::ID, $permissionId);
            }
            $this->permissions[$permissionId] = $permission;
        }
        return $this->permissions[$permissionId];
    }

    /**
     * {@inheritdoc}
     */
    public function getForCmsPage($pageId, $groupId, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $permissionId = $this->resource->getPermissionIdByCmsPageId($pageId, $groupId, $storeId);

        return $permissionId ? $this->getById($permissionId) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CmsPagePermissionCollection $collection */
        $collection = $this->cmsPagePermissionCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CmsPagePermissionInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CmsPagePermissionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var CmsPagePermissionModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param CmsPagePermissionModel $model
     * @return CmsPagePermissionInterface
     */
    private function getDataObject($model)
    {
        /** @var CmsPagePermissionInterface $object */
        $object = $this->cmsPagePermissionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            CmsPagePermissionInterface::class
        );
        return $object;
    }
}
