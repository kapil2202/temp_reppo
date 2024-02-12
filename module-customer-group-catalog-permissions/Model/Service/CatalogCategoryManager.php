<?php

declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Model\Service;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\TreeFactory;
use Aheadworks\CustGroupCatPermissions\Service\CategoryChildren;

class CatalogCategoryManager
{
    private $allChildrenIds = [];
    private $categoryChildren;

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly TreeFactory $categoryTreeFactory,
        private readonly StoreManagerInterface $storeManager,
        CategoryChildren $categoryChildren
    ) {
        $this->categoryChildren = $categoryChildren;
    }

    public function getChildrenIds(CategoryCollection $collection): array
    {
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $categories = $collection->getConnection()->fetchall($collection->getSelect());
        $childrenIds = [];
        $recursionLevel = 0;
        $tree = $this->categoryTreeFactory->create();
        $childrenCategoryIds = [];

        if (!$this->categoryChildren->getIds()) {
            $loadNode = $tree->loadNode($rootCategoryId)->loadChildren($recursionLevel);
            $this->allChildrenIds = $this->collectChildCategoryIds($loadNode, $childrenCategoryIds);
            $this->categoryChildren->setIds($this->allChildrenIds);
        }

        $this->allChildrenIds = $this->categoryChildren->getIds();

        $parentIds = [];

        foreach ($categories as $category) {
            $parentIds[] = $category['entity_id'];
            if (
                isset($this->allChildrenIds[$category['entity_id']])
                && !empty($this->allChildrenIds[$category['entity_id']])
            ) {
                $childCatResult = [];
                $this->processChildrenCategories($this->allChildrenIds[$category['entity_id']], $childCatResult);
                $childrenIds[] = $childCatResult;
            }
        }
        $childrenIds = array_merge([], ...$childrenIds);
        return array_diff($childrenIds, $parentIds);
    }

    private function processChildrenCategories(array $currentCatChildren, array &$childCatResult)
    {
        foreach ($currentCatChildren as $catId) {
            if (key_exists($catId, $this->allChildrenIds)) {
                $childCatResult[] = $catId;
                $this->processChildrenCategories($this->allChildrenIds[$catId], $childCatResult);
            } else {
                $childCatResult[] = $catId;
            }
        }
    }

    /**
     * Collect Child Category Ids
     *
     * @param \Magento\Framework\Data\Tree\Node $childCategory
     * @param array $childrenCategoryIds
     * @return array
     */
    private function collectChildCategoryIds(\Magento\Framework\Data\Tree\Node $childCategory, array &$childrenCategoryIds)
    {
        if ($childCategory->hasChildren()) {
            foreach ($childCategory->getChildren() as $child) {
                if ($child->hasChildren()) {
                    $childrenCategoryIds[$childCategory->getId()][] = $child->getId();
                    $this->collectChildCategoryIds($child, $childrenCategoryIds);
                } else {
                    $childrenCategoryIds[$childCategory->getId()][] = $child->getId();
                }
            }
        }
        return $childrenCategoryIds;
    }
}
