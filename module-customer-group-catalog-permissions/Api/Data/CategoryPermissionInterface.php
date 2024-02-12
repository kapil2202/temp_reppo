<?php
namespace Aheadworks\CustGroupCatPermissions\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CategoryPermissionInterface
 * @package Aheadworks\CustGroupCatPermissions\Api\Data
 */
interface CategoryPermissionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'permission_id';
    const CATEGORY_ID = 'category_id';
    const RECORD_ID = 'record_id';
    const STORE_ID = 'store_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const VIEW_MODE = 'view_mode';
    const PRICE_MODE = 'price_mode';
    const CHECKOUT_MODE = 'checkout_mode';
    const HIDDEN_PRICE_MESSAGE = 'hidden_price_message';
    const HIDDEN_ADD_TO_CART_MESSAGE = 'hidden_add_to_cart_message';
    const PERMISSION_TYPE = 'permission_type';
    /**#@-*/

    /**#@+
     * Types of permissions
     */
    const DEFAULT_PERMISSION_TYPE = 'default';
    const PERSONAL_PERMISSION_TYPE = 'personal';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
     */
    public function getPermissionId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setPermissionId($id);

    /**
     * Get category id
     *
     * @return int
     */
    public function getCategoryId();

    /**
     * Set category id
     *
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * Get record id
     *
     * @return int
     */
    public function getRecordId();

    /**
     * Set record id
     *
     * @param int $recordId
     * @return $this
     */
    public function setRecordId($recordId);

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get customer group id
     *
     * @return int
     */
    public function getCustomerGroupId();

    /**
     * Set customer group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setCustomerGroupId($groupId);

    /**
     * Get view mode
     *
     * @return int
     */
    public function getViewMode();

    /**
     * Set view mode
     *
     * @param int $mode
     * @return $this
     */
    public function setViewMode($mode);

    /**
     * Get price mode
     *
     * @return int
     */
    public function getPriceMode();

    /**
     * Set price mode
     *
     * @param int $mode
     * @return $this
     */
    public function setPriceMode($mode);

    /**
     * Get checkout mode
     *
     * @return int
     */
    public function getCheckoutMode();

    /**
     * Set checkout mode
     *
     * @param int $mode
     * @return $this
     */
    public function setCheckoutMode($mode);

    /**
     * Get hidden price message
     *
     * @return string|null
     */
    public function getHiddenPriceMessage();

    /**
     * Set hidden price message
     *
     * @param string $message
     * @return $this
     */
    public function setHiddenPriceMessage($message);

    /**
     * Get hidden add to cart button message
     *
     * @return string|null
     */
    public function getHiddenAddToCartMessage();

    /**
     * Set hidden add to cart button message
     *
     * @param string $message
     * @return $this
     */
    public function setHiddenAddToCartMessage($message);

    /**
     * Set permission type
     *
     * @param string|null $value
     * @return mixed
     */
    public function setPermissionType($value = null);

    /**
     * Get permission type
     *
     * @return string|null
     */
    public function getPermissionType();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionExtensionInterface $extensionAttributes
    );
}
