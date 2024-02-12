<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\ProductPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver\CurrentCategory as CurrentCategoryResolver;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionResolver;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Resolver as CategoryPermissionResolver;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver\ChildProducts as ChildProductsResolver;
use Magento\Framework\Session\SessionManager;

/**
 * Class Resolver
 * @package Aheadworks\CustGroupCatPermissions\Model\ProductPermission
 */
class Resolver extends AbstractPermissionResolver
{
    /**
     * @var ProductPermissionRepositoryInterface
     */
    private $productPermissionRepository;

    /**
     * @var Converter
     */
    private $permissionConverter;

    /**
     * @var CategoryPermissionResolver
     */
    private $categoryPermissionResolver;

    /**
     * @var CurrentCategoryResolver
     */
    protected $currentCategoryResolver;

    /**
     * @var ChildProductsResolver
     */
    protected $childProductsResolver;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * Resolver constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param Session $session
     * @param ProductPermissionRepositoryInterface $productPermissionRepository
     * @param Converter $converter
     * @param CategoryPermissionResolver $resolver
     * @param CurrentCategoryResolver $currentCategoryResolver
     * @param ChildProductsResolver $childProductsResolver
     * @param ProductRepositoryInterface $productRepository
     * @param SessionManager $sessionManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        Session $session,
        ProductPermissionRepositoryInterface $productPermissionRepository,
        Converter $converter,
        CategoryPermissionResolver $resolver,
        CurrentCategoryResolver $currentCategoryResolver,
        ChildProductsResolver $childProductsResolver,
        ProductRepositoryInterface $productRepository,
        SessionManager $sessionManager
    ) {
        parent::__construct($config, $storeManager, $httpContext, $session);
        $this->productPermissionRepository = $productPermissionRepository;
        $this->permissionConverter = $converter;
        $this->categoryPermissionResolver = $resolver;
        $this->currentCategoryResolver = $currentCategoryResolver;
        $this->childProductsResolver = $childProductsResolver;
        $this->productRepository = $productRepository;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Get permission for product
     *
     * @param Product $product
     * @return ProductPermissionInterface|CategoryPermissionInterface
     */
    public function getPermissionForProduct($product)
    {
        $productId = $product->getId();
        $category = $this->currentCategoryResolver->getCurrentCategory($product);
        $storeId = $this->getStoreId();
        $customerGroupId = $this->getCustomerGroupId();

        try {
            $permission = $this->productPermissionRepository->getForProduct(
                $productId,
                $customerGroupId,
                $storeId
            );
            if ($permission) {
                if ($product->getTypeId() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $this->childProductsResolver->updateBundleProductPricePermission(
                        $permission,
                        $product,
                        $customerGroupId,
                        $storeId
                    );
                }
                $result = $this->resolveMessagesForHiddenBlocks($permission);
            } else {
                if ($product->getParentProductId() && !$this->sessionManager->getIsNotRequestParentProduct()) {
                    $this->sessionManager->setIsNotRequestParentProduct(true);
                    $parentProduct = $this->productRepository->getById($product->getParentProductId());
                    $this->sessionManager->unsIsNotRequestParentProduct();
                    $category = $this->currentCategoryResolver->getCurrentCategory($parentProduct);
                }
                $result = $this->getDefaultPermissionForProduct($category);
            }
        } catch (NoSuchEntityException $e) {
            $result = $this->getDefaultPermissionForProduct($category);
        }

        return $result;
    }

    /**
     * Get default permission for product
     *
     * @param CategoryInterface|array $category
     * @return CategoryPermissionInterface|ProductPermissionInterface
     */
    private function getDefaultPermissionForProduct($category)
    {
        if (is_array($category) && count($category) > 0) {
            $categories = $category;
            $result = $this->getPermissionByCategories($categories);
        } else {
            $result = $category
                ? $this->categoryPermissionResolver->getPermissionForCategory($category)
                : $this->getDefaultPermission();
        }
        return $result;
    }

    /**
     * @param array $categories
     * @return CategoryPermissionInterface
     */
    protected function getPermissionByCategories($categories)
    {
        $defaultPermission = null;
        $notDefaultPermission = null;
        foreach ($categories as $category) {
            $result = $this->categoryPermissionResolver->getPermissionForCategory($category);
            if ($result->getPermissionType() !== CategoryPermissionInterface::DEFAULT_PERMISSION_TYPE) {
                $notDefaultPermission = $result;
                break;
            }
            if ($result->getViewMode() == AccessMode::HIDE || $result->getPriceMode() == AccessMode::HIDE) {
                $defaultPermission = $result;
            }
        }

        if ($notDefaultPermission) {
            $result = $notDefaultPermission;
        } elseif ($defaultPermission) {
            $result = $defaultPermission;
        }

        return $result ?? $this->categoryPermissionResolver->getDefaultPermission();
    }

    /**
     * {@inheritdoc}
     */
    protected function convertDefaultPermissionToObject($permissionData)
    {
        return $this->permissionConverter->getDataObject($permissionData);
    }
}
