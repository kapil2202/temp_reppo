<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission as PermissionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as CatalogSearchCollection;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;

/**
 * Class Applier
 * @package Aheadworks\CustGroupCatPermissions\Model\ProductPermission
 */
class Applier
{
    /**#@+
     * Constants defined for keys of the data array.
     */
    const PERMISSION_APPLIED = 'permission_applied';
    const HIDE_PRODUCT = 'hide_product';
    const HIDE_PRICE = 'hide_price';
    const HIDE_ADD_TO_CART = 'hide_add_to_cart';
    const SKIP_PERMISSION_APPLY = 'skip_permission_apply';
    /**#@-*/

    /**#@+
     * Field name for product collection.
     */
    const IN_ENTITY_ID = 'entity_id';
    const NIN_ENTITY_ID = 'nin_entity_id';
    /**#@-*/

    /**
     * @var Resolver
     */
    private $permissionResolver;

    /**
     * @var PermissionResource
     */
    private $resource;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Resolver $resolver
     * @param PermissionResource $resource
     * @param Registry $registry
     */
    public function __construct(
        Resolver $resolver,
        PermissionResource $resource,
        Registry $registry
    ) {
        $this->permissionResolver = $resolver;
        $this->resource = $resource;
        $this->registry = $registry;
    }

    /**
     * Apply permission for product
     *
     * @param Product $product
     */
    public function applyForProduct($product)
    {
        if (!$product->getData(self::PERMISSION_APPLIED)
            && !$this->registry->registry(self::SKIP_PERMISSION_APPLY)
        ) {
            /** @var ProductPermissionInterface $permission */
            $permission = $this->permissionResolver->getPermissionForProduct($product);
            if ($permission) {
                if ($permission->getViewMode() == AccessMode::HIDE) {
                    $product->setIsSalable(false);
                    $product->setData(self::HIDE_PRODUCT, true);
                } elseif ($permission->getPriceMode() == AccessMode::HIDE) {
                    $product->setData(self::HIDE_PRICE, true);
                    $product->setData(
                        ProductPermissionInterface::HIDDEN_PRICE_MESSAGE,
                        $permission->getHiddenPriceMessage()
                    );
                } elseif ($permission->getCheckoutMode() == AccessMode::HIDE) {
                    $product->setData(self::HIDE_ADD_TO_CART, true);
                    $product->setData(
                        ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE,
                        $permission->getHiddenAddToCartMessage()
                    );
                }
                $product->setData(self::PERMISSION_APPLIED, true);
            }
        }
    }

    /**
     * Apply permissions for product collection
     *
     * @param AbstractCollection $collection
     */
    public function applyForCollection($collection)
    {
        if (!$collection->getFlag(self::PERMISSION_APPLIED)
            && !$this->registry->registry(self::SKIP_PERMISSION_APPLY)) {
            $isCatalogSearchCollection = $collection instanceof CatalogSearchCollection;

            if ($isCatalogSearchCollection) {
                $this->applyForCatalogSearchCollection($collection);
            } else {
                $this->applyForCatalogCollection($collection);
            }
            $collection->setFlag(self::PERMISSION_APPLIED, true);
        }
    }

    /**
     * Apply permissions for catalog search collection
     *
     * @param AbstractCollection $collection
     */
    private function applyForCatalogSearchCollection($collection)
    {
        list($storeId, $customerGroupId, $defaultViewMode) = $this->permissionResolver->getRequiredFilterValues();
        list($fieldName, $productIds) =
            $this->resource->getProductsFilterForSearch($storeId, $customerGroupId, $defaultViewMode);

        if (!empty($productIds)) {
            $collection->addFieldToFilter($fieldName, $productIds);
        }
    }
    /**
     * Apply permissions for catalog collection
     *
     * @param AbstractCollection $collection
     */
    private function applyForCatalogCollection($collection)
    {
        list($storeId, $customerGroupId, $defaultViewMode) = $this->permissionResolver->getRequiredFilterValues();
        $isProductChildren = $collection->hasFlag('product_children');
        $filter = $this->resource->getProductsFilter($storeId, $customerGroupId, $defaultViewMode, $isProductChildren);

        $collection->addFieldToFilter('entity_id', $filter);
    }
}
