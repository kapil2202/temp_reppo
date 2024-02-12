<?php
namespace Aheadworks\CustGroupCatPermissions\Model\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission as PermissionResource;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\Flat\Collection as FlatCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Catalog\Model\Category;
use Aheadworks\CustGroupCatPermissions\Model\Service\CatalogCategoryManager;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Applier
 * @package Aheadworks\CustGroupCatPermissions\Model\CategoryPermission
 */
class Applier
{
    /**#@+
     * Constants defined for keys of the data array.
     */
    const PERMISSION_APPLIED = 'permission_applied';
    const HIDE_PRICE = 'hide_price';
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
     * @var CatalogCategoryManager
     */
    private $catalogCategoryManager;

    /**
     * Applier constructor.
     *
     * @param Resolver $resolver
     * @param PermissionResource $resource
     * @param CatalogCategoryManager $catalogCategoryManager
     */
    public function __construct(
        Resolver $resolver,
        PermissionResource $resource,
        CatalogCategoryManager $catalogCategoryManager
    ) {
        $this->permissionResolver = $resolver;
        $this->resource = $resource;
        $this->catalogCategoryManager = $catalogCategoryManager;
    }

    /**
     * Apply permission for category
     *
     * @param Category $category
     */
    public function applyForCategory($category)
    {
        if (!$category->getData(self::PERMISSION_APPLIED)) {
            /** @var CategoryPermissionInterface $permission */
            $permission = $this->permissionResolver->getPermissionForCategory($category);
            if ($permission) {
                if ($permission->getViewMode() == AccessMode::HIDE) {
                    $category->setIsActive(false);
                }
                if ($permission->getPriceMode() == AccessMode::HIDE) {
                    $category->setData(self::HIDE_PRICE, true);
                }
                $category->setData(self::PERMISSION_APPLIED, true);
            }
        }
    }

    /**
     * Apply permissions for category collection
     *
     * @param AbstractCollection $collection
     * @param bool $withChildren
     * @throws NoSuchEntityException
     */
    public function applyForCollection($collection, $withChildren = true)
    {
        if (!$collection->getFlag(self::PERMISSION_APPLIED)) {
            list($storeId, $customerGroupId, $defaultViewMode) = $this->permissionResolver->getRequiredFilterValues();
            $categoriesFilter = $this->resource->getCategoriesFilter($storeId, $customerGroupId, $defaultViewMode);
            $fieldName = $this->getEntityField($collection);
            $childrenFilter = null;
            if ($withChildren) {
                $clonedCollection = clone $collection;
                $clonedCollection->addFieldToFilter($fieldName, $categoriesFilter);
                $childrenIds = $this->catalogCategoryManager->getChildrenIds($clonedCollection);
                $childrenFilter = $this->resource->getChildrenCategoriesFilter($childrenIds);
            }

            $filterCondition = $childrenFilter ? [$categoriesFilter, $childrenFilter] : $categoriesFilter;

            $collection->addFieldToFilter($fieldName, $filterCondition);
            $collection->setFlag(self::PERMISSION_APPLIED, true);
        }
    }

    /**
     * Retrieve entity field
     *
     * @param Collection|FlatCollection|AbstractCollection $collection
     * @return string
     */
    private function getEntityField($collection)
    {
        $field = 'entity_id';

        if ($collection instanceof FlatCollection) {
            $field = 'main_table.' . $field;
        }

        return $field;
    }

    /**
     * Apply for category flat load nodes db select
     *
     * @param Select $select
     * @return void
     */
    public function applyForLoadNodesDbSelect(Select $select): void
    {
        list($storeId, $customerGroupId, $defaultViewMode) = $this->permissionResolver->getRequiredFilterValues();
        $filter = $this->resource->getCategoriesFilter($storeId, $customerGroupId, $defaultViewMode);
        $condition = array_key_first($filter);
        $subQuery = $filter[$condition];
        $condition = $condition === 'nin' ? 'NOT IN' : $condition;

        $select->where(sprintf('main_table.entity_id %s (?)', $condition), $subQuery);
    }
}
