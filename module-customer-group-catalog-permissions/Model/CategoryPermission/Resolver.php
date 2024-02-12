<?php
namespace Aheadworks\CustGroupCatPermissions\Model\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\CategoryPermissionRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\AbstractPermissionResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;

/**
 * Class Resolver
 * @package Aheadworks\CustGroupCatPermissions\Model\CategoryPermission
 */
class Resolver extends AbstractPermissionResolver
{
    /**
     * @var CategoryPermissionRepositoryInterface
     */
    private $categoryPermissionRepository;

    /**
     * @var Converter
     */
    private $permissionConverter;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param Session $session
     * @param CategoryPermissionRepositoryInterface $categoryPermissionRepository
     * @param Converter $converter
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        Session $session,
        CategoryPermissionRepositoryInterface $categoryPermissionRepository,
        Converter $converter
    ) {
        parent::__construct($config, $storeManager, $httpContext, $session);
        $this->categoryPermissionRepository = $categoryPermissionRepository;
        $this->permissionConverter = $converter;
    }

    /**
     * Get permission for category
     *
     * @param Category $category
     * @return CategoryPermissionInterface
     */
    public function getPermissionForCategory($category)
    {
        $categoryId = $category->getId();
        $parentIds = $category->getPathIds();
        $storeId = $this->getStoreId();
        $customerGroupId = $this->getCustomerGroupId();

        try {
            $permission = $this->categoryPermissionRepository->getForCategory(
                $categoryId,
                $parentIds,
                $customerGroupId,
                $storeId
            );
            if ($permission) {
                $result = $this->resolveMessagesForHiddenBlocks($permission);
            } else {
                $result = $this->getDefaultPermission();
            }
        } catch (NoSuchEntityException $e) {
            $result = $this->getDefaultPermission();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function convertDefaultPermissionToObject($permissionData)
    {
        return $this->permissionConverter->getDataObject($permissionData);
    }
}
