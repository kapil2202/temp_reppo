<?php
namespace Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionConverter;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;

/**
 * Class Converter
 * @package Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission
 */
class Converter extends AbstractPermissionConverter
{
    /**
     * @var string
     */
    protected $relatedObjectKey = CmsPagePermissionInterface::CMS_PAGE_ID;

    /**
     * @var CmsPagePermissionInterfaceFactory
     */
    private $cmsPagePermissionInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CmsPagePermissionInterfaceFactory $cmsPagePermissionInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CmsPagePermissionInterfaceFactory $cmsPagePermissionInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->cmsPagePermissionInterfaceFactory = $cmsPagePermissionInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function preparePermissionsArrayToSave($permissionsArray)
    {
        return $permissionsArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function preparePermissionsArrayToDisplay($permissionsArray)
    {
        $result = parent::preparePermissionsArrayToDisplay($permissionsArray);
        return array_shift($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareObjects(array $permissionData, $objectId)
    {
        $objects = [];
        $permissionObject = new DataObject($permissionData);
        $storeIds = $permissionObject->getStoreIds();
        $customerGroupIds = $permissionObject->getCustomerGroupIds();
        $viewMode = $permissionObject->getViewMode();

        foreach ($storeIds as $storeId) {
            foreach ($customerGroupIds as $groupId) {
                $permission = [
                    $this->relatedObjectKey => $objectId,
                    CmsPagePermissionInterface::STORE_ID => $storeId,
                    CmsPagePermissionInterface::CUSTOMER_GROUP_ID => $groupId,
                    CmsPagePermissionInterface::VIEW_MODE => $viewMode
                ];
                $objects[] = $this->getDataObject($permission);
            }
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    protected function getLinkageKey(array $permission)
    {
        return $permission[CmsPagePermissionInterface::CMS_PAGE_ID];
    }

    /**
     * Convert permissions data to object
     *
     * @param array $permissionData
     * @return CmsPagePermissionInterface
     */
    public function getDataObject(array $permissionData)
    {
        /** @var CmsPagePermissionInterface $object */
        $object = $this->cmsPagePermissionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $permissionData,
            CmsPagePermissionInterface::class
        );
        return $object;
    }
}
