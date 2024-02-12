<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver;

use Aheadworks\CustGroupCatPermissions\Model\Service\ResponseManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Category;

/**
 * Class CurrentCategory
 * @package Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver
 */
class CurrentCategory
{
    /**
     * CurrentCategory constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ResponseManager $responseManager
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ResponseManager $responseManager,
        private CategoryCollectionFactory $categoryCollectionFactory
    ) {
    }

    /**
     * Retrieve current category object
     *
     * @param Product $product
     * @return CategoryInterface|array
     */
    public function getCurrentCategory($product)
    {
        $currentCategory = null;
        $currentCategoryId = $product->getCategoryId();

        try {
            if ($currentCategoryId) {
                $currentCategory = $this->categoryRepository->get($currentCategoryId);
            }
        } catch (NoSuchEntityException $e) {
            $currentCategory = null;
        }

        return $currentCategory ?? $product->getCategoryCollection()->getItems();
    }

    /**
     * Retrieve current category id when FPC enabled
     *
     * @return int|null
     */
    public function getCurrentCategoryIdFpc()
    {
        $urlKey = $this->responseManager->getCurrentRedirectRefererUrlKey();
        return $this->getCategoryIdByUrlKey($urlKey);
    }

    /**
     * Retrieve category id by url key
     *
     * @param string $urlKey
     * @return int|null
     */
    private function getCategoryIdByUrlKey($urlKey)
    {
        $categoryId = null;

        if (!empty($urlKey)) {
            try {
                /** @var CategoryCollection $categoryCollection */
                $categoryCollection = $this->categoryCollectionFactory->create();
                $categoryCollection->addAttributeToFilter('url_key', $urlKey);
                /** @var Category $category */
                $category = $categoryCollection->getFirstItem();
                if (is_object($category)) {
                    $categoryId = $category->getId();
                }
            } catch (\Exception $exception) {
            }
        }

        return $categoryId;
    }
}
