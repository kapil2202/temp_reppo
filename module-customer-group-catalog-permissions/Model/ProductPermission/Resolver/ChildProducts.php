<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionResolver;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CustGroupCatPermissions\Api\ProductPermissionRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Converter;

/**
 * Class ChildProducts resolver
 */
class ChildProducts extends AbstractPermissionResolver
{
    /**
     * @var ProductLinkManagementInterface
     */
    private $productLinkManagement;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ProductPermissionRepositoryInterface
     */
    private $productPermissionRepository;

    /**
     * @var Converter
     */
    private $permissionConverter;

    /**
     * ChildProducts constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param Session $session
     * @param ProductLinkManagementInterface $productLinkManagement
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductPermissionRepositoryInterface $productPermissionRepository
     * @param Converter $converter
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        Session $session,
        ProductLinkManagementInterface $productLinkManagement,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductPermissionRepositoryInterface $productPermissionRepository,
        Converter $converter
    ) {
        parent::__construct($config, $storeManager, $httpContext, $session);
        $this->productLinkManagement = $productLinkManagement;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productPermissionRepository = $productPermissionRepository;
        $this->permissionConverter = $converter;
    }

    /**
     * Update bundle product price permission according child products price permissions
     *
     * @param ProductPermissionInterface $permission
     * @param Product $product
     * @param int $groupId
     * @param int|null $storeId
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function updateBundleProductPricePermission($permission, $product, $groupId, $storeId = null): void
    {
        if ($product->getTypeId() !== \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return;
        }

        if (!$storeId) {
            $storeId = $this->getStoreId();
        }

        $childProducts = $this->getChildProductsByParent($product);
        $childPermissions = $this->getProductsPermissions($childProducts, $groupId, $storeId);

        if (count($childPermissions) === 0) {
            // if permissions not found for child products then will apply default price permission to parent product
            $configPermission = $this->getDefaultPermission();
            $permission->setPriceMode($configPermission->getPriceMode());
        } else {
            // if all child products prices hidden then parent product price will be hide
            $countHiddenPriceProducts = 0;
            foreach ($childPermissions as $childPermission) {
                if ((int)$childPermission->getPriceMode() === AccessMode::HIDE) {
                    $countHiddenPriceProducts ++;
                }
            }
            if ($countHiddenPriceProducts === count($childProducts)) {
                $permission->setPriceMode(AccessMode::HIDE);
            }
        }
    }

    /**
     * Get child products by parent product
     *
     * @param Product $product
     * @return \Magento\Bundle\Api\Data\LinkInterface[]
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getChildProductsByParent($product): array
    {
        return $this->productLinkManagement->getChildren($product->getSku());
    }

    /**
     * Get products permissions
     *
     * @param array $products
     * @param int $groupId
     * @param int|null $storeId
     * @return array
     */
    private function getProductsPermissions($products, $groupId, $storeId): array
    {
        try {
            $productIds = [];
            foreach ($products as $product) {
                $productIds[] = $product->getEntityId();
            }

            $nullVal = new \Zend_Db_Expr('NULL');
            $filters = [
                $this->filterBuilder
                    ->setField(ProductPermissionInterface::PRODUCT_ID)
                    ->setConditionType('in')
                    ->setValue($productIds)
                    ->create(),
                $this->filterBuilder
                    ->setField(ProductPermissionInterface::CUSTOMER_GROUP_ID)
                    ->setConditionType('in')
                    ->setValue([$groupId, $nullVal])
                    ->create(),
                $this->filterBuilder
                    ->setField(ProductPermissionInterface::STORE_ID)
                    ->setConditionType('in')
                    ->setValue([$storeId, $nullVal])
                    ->create()
            ];

            $this->searchCriteriaBuilder->addFilters($filters);
            $searchCriteria = $this->searchCriteriaBuilder->create();

            return $this->productPermissionRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $ex) {
            return [];
        }
    }

    /**
     * Convert permission to object
     *
     * @param array $permissionData
     * @return ProductPermissionInterface
     */
    protected function convertDefaultPermissionToObject($permissionData): ProductPermissionInterface
    {
        return $this->permissionConverter->getDataObject($permissionData);
    }
}
