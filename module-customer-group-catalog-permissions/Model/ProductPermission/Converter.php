<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionConverter;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Converter
 * @package Aheadworks\CustGroupCatPermissions\Model\ProductPermission
 */
class Converter extends AbstractPermissionConverter
{
    /**
     * @var string
     */
    protected $relatedObjectKey = ProductPermissionInterface::PRODUCT_ID;

    /**
     * @var ProductPermissionInterfaceFactory
     */
    private $productPermissionInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param ProductPermissionInterfaceFactory $productPermissionInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        ProductPermissionInterfaceFactory $productPermissionInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->productPermissionInterfaceFactory = $productPermissionInterfaceFactory;
    }

    /**
     * Convert permissions data to object
     *
     * @param array $permissionData
     * @return ProductPermissionInterface
     */
    public function getDataObject(array $permissionData)
    {
        /** @var ProductPermissionInterface $object */
        $object = $this->productPermissionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $permissionData,
            ProductPermissionInterface::class
        );
        return $object;
    }
}
