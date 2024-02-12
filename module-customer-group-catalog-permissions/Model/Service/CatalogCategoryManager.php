<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Model\Service;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\TreeFactory;

class CatalogCategoryManager
{
    /**
     * Save all available of child categories id
     *
     * @var array
     */
    private $allChildrenIds = [];

    /**
     * CatalogCategoryManager constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param TreeFactory $categoryTreeFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly TreeFactory $categoryTreeFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Get children ids from category collection
     *
     * @param AbstractCollection $collection
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChildrenIds($collection): array
    {
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $categories = $collection->getConnection()->fetchall($collection->getSelect());
        $childrenIds = [];
        $recursionLevel = 0;
        $tree = $this->categoryTreeFactory->create();
        $loadNode = $tree->loadNode($rootCategoryId)->loadChildren($recursionLevel);
        $childrenCategoryIds = [];
        $parentIds = [];
        $this->allChildrenIds = $this->collectChildCategoryIds($loadNode, $childrenCategoryIds);
        foreach ($categories as $category) {
            $parentIds[] = $category['entity_id'];
            if (isset($this->allChildrenIds[$category['entity_id']])
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

    /**
     * Processs Children Categories
     *
     * @param array $currentCatChildren
     * @param array $childCatResult
     * @return void
     */
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
