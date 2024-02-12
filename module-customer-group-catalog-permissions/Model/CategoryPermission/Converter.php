<?php
namespace Aheadworks\CustGroupCatPermissions\Model\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionConverter;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Converter
 * @package Aheadworks\CustGroupCatPermissions\Model\CategoryPermission
 */
class Converter extends AbstractPermissionConverter
{
    /**
     * @var string
     */
    protected $relatedObjectKey = CategoryPermissionInterface::CATEGORY_ID;

    /**
     * @var CategoryPermissionInterfaceFactory
     */
    private $categoryPermissionInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CategoryPermissionInterfaceFactory $categoryPermissionInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CategoryPermissionInterfaceFactory $categoryPermissionInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->categoryPermissionInterfaceFactory = $categoryPermissionInterfaceFactory;
    }

    /**
     * Convert permissions data to object
     *
     * @param array $permissionData
     * @return CategoryPermissionInterface
     */
    public function getDataObject(array $permissionData)
    {
        /** @var CategoryPermissionInterface $object */
        $object = $this->categoryPermissionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $permissionData,
            CategoryPermissionInterface::class
        );
        return $object;
    }
}
