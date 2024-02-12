<?php
namespace Aheadworks\CustGroupCatPermissions\Model\NativePermissions;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class Resolver
 *
 * @package Aheadworks\CustGroupCatPermissions\Model\NativePermissions
 */
class Resolver
{
    /**
     * EE catalog permissions module name
     */
    const NATIVE_CATALOG_PERMISSIONS_MODULE_NAME = 'Magento_CatalogPermissions';

    /**
     * EE catalog permissions config class name
     */
    const NATIVE_CATALOG_PERMISSIONS_CONFIG_CLASS_NAME  = '\Magento\CatalogPermissions\App\Config';

    /**
     * Module manager
     *
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ModuleManager $moduleManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ModuleManager $moduleManager,
        UrlInterface $urlBuilder
    ) {
        $this->moduleManager = $moduleManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Check if EE catalog permissions module installed
     *
     * @return bool
     */
    public function isEnterpriseCatalogPermissionsInstalled()
    {
        return $this->moduleManager->isEnabled(self::NATIVE_CATALOG_PERMISSIONS_MODULE_NAME);
    }

    /**
     * Check whether enterprise catalog permissions functionality should be enabled
     *
     * @return bool
     */
    public function isEnterpriseCatalogPermissionsEnabled()
    {
        $flag = false;
        if ($this->isEnterpriseCatalogPermissionsInstalled()) {
            $enterpriseCatalogPermissionsConfig = $this->getEnterpriseCatalogPermissionsConfig();
            if (is_object($enterpriseCatalogPermissionsConfig)) {
                $flag = $enterpriseCatalogPermissionsConfig->isEnabled();
            }
        }
        return $flag;
    }

    /**
     * Retrieve instance of catalog permissions config class
     *
     * @return mixed|null
     */
    public function getEnterpriseCatalogPermissionsConfig()
    {
        $enterpriseCatalogPermissionsConfig = null;
        if ($this->isEnterpriseCatalogPermissionsInstalled()) {
            $enterpriseCatalogPermissionsConfig = ObjectManager::getInstance()
                ->create(self::NATIVE_CATALOG_PERMISSIONS_CONFIG_CLASS_NAME)
            ;
        }
        return $enterpriseCatalogPermissionsConfig;
    }

    /**
     * Retrieve url of EE catalog permissions settings page
     *
     * @return string
     */
    public function getEnterpriseCatalogPermissionsSettingsPageUrl()
    {
        return $this->urlBuilder->getUrl(
            $this->getEnterpriseCatalogPermissionsSettingsRoutePath(),
            ['_fragment' => $this->getEnterpriseCatalogPermissionsSettingsUrlFragment()]
        );
    }

    /**
     * Retrieve route path of EE catalog permissions settings page
     *
     * @return string
     */
    private function getEnterpriseCatalogPermissionsSettingsRoutePath()
    {
        return 'adminhtml/system_config/edit/section/catalog';
    }

    /**
     * Retrieve fragment parameter for EE catalog permissions settings page url
     *
     * @return string
     */
    private function getEnterpriseCatalogPermissionsSettingsUrlFragment()
    {
        return 'catalog_magento_catalogpermissions-link';
    }
}
