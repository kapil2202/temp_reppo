<?php
namespace Aheadworks\CustGroupCatPermissions\Model;

use Magento\Cms\Helper\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\CustGroupCatPermissions\Model
 */
class Config
{
    /**#@+
     * Constants for config paths
     */
    const XML_PATH_GENERAL_ENABLE = 'aw_cat_permissions/general/enable';
    const XML_PATH_CATALOG_BROWSING = 'aw_cat_permissions/catalog_permissions/browsing';
    const XML_PATH_CATALOG_BROWSING_CUSTOMER_GROUPS
        = 'aw_cat_permissions/catalog_permissions/browsing_customer_groups';
    const XML_PATH_CATALOG_BROWSING_REDIRECT_URL
        = 'aw_cat_permissions/catalog_permissions/browsing_redirect_page_url';
    const XML_PATH_CATALOG_PRODUCT_PRICE_DISPLAYING
        = 'aw_cat_permissions/catalog_permissions/product_price_displaying';
    const XML_PATH_CATALOG_PRODUCT_PRICE_DISPLAYING_CUSTOMER_GROUPS
        = 'aw_cat_permissions/catalog_permissions/product_price_displaying_customer_groups';
    const XML_PATH_CATALOG_PRODUCT_PRICE_DISPLAYING_MESSAGE_FOR_HIDDEN_PRICE
        = 'aw_cat_permissions/catalog_permissions/product_price_displaying_message_for_hidden_price';
    const XML_PATH_CATALOG_PRODUCT_ADD_TO_CART_BUTTON_DISPLAYING
        = 'aw_cat_permissions/catalog_permissions/product_add_to_cart_button_displaying';
    const XML_PATH_CATALOG_PRODUCT_ADD_TO_CART_BUTTON_DISPLAYING_CUSTOMER_GROUPS
        = 'aw_cat_permissions/catalog_permissions/product_add_to_cart_button_displaying_customer_groups';
    const XML_PATH_CATALOG_PRODUCT_ADD_TO_CART_BUTTON_DISPLAYING_MESSAGE_FOR_HIDDEN_BUTTON
        = 'aw_cat_permissions/catalog_permissions/product_add_to_cart_button_displaying_message_for_hidden_button';
    const XML_PATH_CMS_PAGE_BROWSING = 'aw_cat_permissions/cms_page_permissions/cms_page_browsing';
    const XML_PATH_CMS_PAGE_BROWSING_CUSTOMER_GROUPS
        = 'aw_cat_permissions/cms_page_permissions/cms_page_browsing_customer_groups';
    const XML_PATH_CMS_PAGE_BROWSING_REDIRECT_URL
        = 'aw_cat_permissions/cms_page_permissions/cms_page_browsing_redirect_page_url';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if module functionality is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLE
        );
    }

    /**
     * Retrieve category browsing mode
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCatalogElementBrowsingMode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_BROWSING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve customer groups, assigned according to the selected browsing mode
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCatalogElementBrowsingCustomerGroups($storeId = null)
    {
        $multiselectConfigValue = $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_BROWSING_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->getParsedMultiselectConfigValue($multiselectConfigValue);
    }

    /**
     * Retrieve category browsing redirect url
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCatalogElementBrowsingRedirectUrl($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_BROWSING_REDIRECT_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve category product price displaying mode
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCatalogElementProductPriceDisplayingMode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRODUCT_PRICE_DISPLAYING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve customer groups, assigned according to the selected product price displaying mode
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCatalogElementProductPriceDisplayingCustomerGroups($storeId = null)
    {
        $multiselectConfigValue = $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRODUCT_PRICE_DISPLAYING_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->getParsedMultiselectConfigValue($multiselectConfigValue);
    }

    /**
     * Retrieve category product price replacing message
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCatalogElementProductPriceDisplayingMessageForHiddenPrice($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRODUCT_PRICE_DISPLAYING_MESSAGE_FOR_HIDDEN_PRICE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve category product add to cart button displaying mode
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCatalogElementProductAddToCartButtonDisplayingMode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRODUCT_ADD_TO_CART_BUTTON_DISPLAYING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve customer groups, assigned according to the selected product add to cart button displaying mode
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCatalogElementProductAddToCartButtonDisplayingCustomerGroups($storeId = null)
    {
        $multiselectConfigValue = $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRODUCT_ADD_TO_CART_BUTTON_DISPLAYING_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->getParsedMultiselectConfigValue($multiselectConfigValue);
    }

    /**
     * Retrieve category product add to cart button replacing message
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRODUCT_ADD_TO_CART_BUTTON_DISPLAYING_MESSAGE_FOR_HIDDEN_BUTTON,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve cms page browsing mode
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCmsPageBrowsingMode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CMS_PAGE_BROWSING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve customer groups, assigned according to the selected cms page browsing mode
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCmsPageBrowsingCustomerGroups($storeId = null)
    {
        $multiselectConfigValue = $this->scopeConfig->getValue(
            self::XML_PATH_CMS_PAGE_BROWSING_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->getParsedMultiselectConfigValue($multiselectConfigValue);
    }

    /**
     * Retrieve cms page browsing redirect url
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCmsPageBrowsingRedirectUrl($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CMS_PAGE_BROWSING_REDIRECT_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve no route CMS page identifier
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNoRouteCmsPageIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue(
            Page::XML_PATH_NO_ROUTE_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve parsed value of multiselect config field
     *
     * @param mixed $multiselectConfigValue
     * @return array
     */
    private function getParsedMultiselectConfigValue($multiselectConfigValue)
    {
        return
            (empty($multiselectConfigValue) && (strlen((string) $multiselectConfigValue) == 0))
                ? []
                : explode(',', (string) $multiselectConfigValue);
    }
}
