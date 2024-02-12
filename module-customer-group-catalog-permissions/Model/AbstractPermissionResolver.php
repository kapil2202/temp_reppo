<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Aheadworks\CustGroupCatPermissions\Model\Source\Config\AccessMode as ConfigAccessSource;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;

/**
 * Class AbstractPermissionResolver
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
abstract class AbstractPermissionResolver
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var int
     */
    protected $customerGroupId;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param Session $session
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        Session $session
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->session = $session;
    }

    /**
     * Get required filter values
     *
     * @return array
     */
    public function getRequiredFilterValues()
    {
        $storeId = $this->getStoreId();
        $customerGroupId = $this->getCustomerGroupId();
        $defaultViewMode = $this->resolveConfigViewMode($storeId, $customerGroupId);

        return [
            $storeId,
            $customerGroupId,
            $defaultViewMode
        ];
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    protected function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->storeManager->getStore(true)->getId();
        }

        return $this->storeId;
    }

    /**
     * Retrieve current customer group id
     *
     * @return int
     */
    protected function getCustomerGroupId()
    {
        if ($this->customerGroupId === null) {
            $this->customerGroupId = $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP)
                ? $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP)
                : $this->session->getCustomerGroupId();
        }

        return $this->customerGroupId;
    }

    /**
     * Resolve messages for hidden blocks
     *
     * @param CategoryPermissionInterface $permission
     * @return CategoryPermissionInterface
     */
    protected function resolveMessagesForHiddenBlocks($permission)
    {
        if ($permission->getHiddenPriceMessage() === null) {
            $permission->setHiddenPriceMessage(
                $this->config->getCatalogElementProductPriceDisplayingMessageForHiddenPrice($this->getStoreId())
            );
        }
        if ($permission->getHiddenAddToCartMessage() === null) {
            $permission->setHiddenAddToCartMessage(
                $this->config->getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton(
                    $this->getStoreId()
                )
            );
        }
        return $permission;
    }

    /**
     * Retrieve default permission
     *
     * @return CategoryPermissionInterface
     */
    protected function getDefaultPermission()
    {
        $storeId = $this->getStoreId();
        $customerGroupId = $this->getCustomerGroupId();

        $permissionData = [
            CategoryPermissionInterface::VIEW_MODE => $this->resolveConfigViewMode($storeId, $customerGroupId),
            CategoryPermissionInterface::PRICE_MODE => $this->resolveConfigPriceMode($storeId, $customerGroupId),
            CategoryPermissionInterface::CHECKOUT_MODE => $this->resolveConfigCheckoutMode($storeId, $customerGroupId),
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE =>
                $this->config->getCatalogElementProductPriceDisplayingMessageForHiddenPrice($storeId),
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE =>
                $this->config->getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton($storeId),
            CategoryPermissionInterface::PERMISSION_TYPE => CategoryPermissionInterface::DEFAULT_PERMISSION_TYPE
        ];

        return $this->convertDefaultPermissionToObject($permissionData);
    }

    /**
     * Resolve view mode value
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return int
     */
    protected function resolveConfigViewMode($storeId, $customerGroupId)
    {
        $viewModeConfigValue = $this->config->getCatalogElementBrowsingMode($storeId);
        $customerGroupIds = $this->config->getCatalogElementBrowsingCustomerGroups($storeId);

        return $this->resolveConfigModeValue($viewModeConfigValue, $customerGroupId, $customerGroupIds);
    }

    /**
     * Resolve price mode value
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return int
     */
    protected function resolveConfigPriceMode($storeId, $customerGroupId)
    {
        $priceModeConfigValue = $this->config->getCatalogElementProductPriceDisplayingMode($storeId);
        $customerGroupIds = $this->config->getCatalogElementProductPriceDisplayingCustomerGroups($storeId);

        return $this->resolveConfigModeValue($priceModeConfigValue, $customerGroupId, $customerGroupIds);
    }

    /**
     * Resolve checkout mode value
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return int
     */
    protected function resolveConfigCheckoutMode($storeId, $customerGroupId)
    {
        $checkoutModeConfigValue = $this->config->getCatalogElementProductAddToCartButtonDisplayingMode($storeId);
        $customerGroupIds = $this->config->getCatalogElementProductAddToCartButtonDisplayingCustomerGroups($storeId);

        return $this->resolveConfigModeValue($checkoutModeConfigValue, $customerGroupId, $customerGroupIds);
    }

    /**
     * Resolve config mode value
     *
     * @param int $modeValue
     * @param int $customerGroupId
     * @param array $customerGroupIds
     * @return int
     */
    protected function resolveConfigModeValue($modeValue, $customerGroupId, $customerGroupIds)
    {
        if ($modeValue == ConfigAccessSource::HIDE_FROM_EVERYONE) {
            $modeValue = AccessMode::HIDE;
        } elseif ($modeValue == ConfigAccessSource::HIDE_FROM_SPECIFIED_CUSTOMER_GROUPS
            && in_array($customerGroupId, $customerGroupIds)
        ) {
            $modeValue = AccessMode::HIDE;
        } else {
            $modeValue = AccessMode::SHOW;
        }

        return $modeValue;
    }

    /**
     * Convert permission to object
     *
     * @param array $permissionData
     * @return mixed
     */
    abstract protected function convertDefaultPermissionToObject($permissionData);
}
