<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission as PermissionResource;

/**
 * Class CategoryPermission
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class CategoryPermission extends AbstractModel implements CategoryPermissionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(PermissionResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermissionId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordId()
    {
        return $this->getData(self::RECORD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecordId($recordId)
    {
        return $this->setData(self::RECORD_ID, $recordId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupId($groupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $groupId);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewMode()
    {
        return $this->getData(self::VIEW_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewMode($mode)
    {
        return $this->setData(self::VIEW_MODE, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceMode()
    {
        return $this->getData(self::PRICE_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceMode($mode)
    {
        return $this->setData(self::PRICE_MODE, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutMode()
    {
        return $this->getData(self::CHECKOUT_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutMode($mode)
    {
        return $this->setData(self::CHECKOUT_MODE, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getHiddenPriceMessage()
    {
        return $this->getData(self::HIDDEN_PRICE_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setHiddenPriceMessage($message)
    {
        return $this->setData(self::HIDDEN_PRICE_MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getHiddenAddToCartMessage()
    {
        return $this->getData(self::HIDDEN_ADD_TO_CART_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setHiddenAddToCartMessage($message)
    {
        return $this->setData(self::HIDDEN_ADD_TO_CART_MESSAGE, $message);
    }

    /**
     * Set permission type
     *
     * @param string|null $value
     * @return mixed
     */
    public function setPermissionType($value = null)
    {
        return $this->setData(self::PERMISSION_TYPE, $value);
    }

    /**
     * Get permission type
     *
     * @return string|null
     */
    public function getPermissionType()
    {
        return $this->getData(self::PERMISSION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
