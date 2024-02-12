<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\DataProvider\Product\Form;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider as ProductFormDataProvider;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermissionManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class ProductDataProviderPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Catalog\DataProviderPlugin\Product\Form
 */
class ProductDataProviderPlugin
{
    /**
     * @var ProductPermissionManager
     */
    private $permissionManager;

    /**
     * @param ProductPermissionManager $processor
     */
    public function __construct(
        ProductPermissionManager $processor
    ) {
        $this->permissionManager = $processor;
    }

    /**
     * Modify data
     *
     * @param ProductFormDataProvider $subject
     * @param array $data
     * @return array
     */
    public function afterGetData(ProductFormDataProvider $subject, $data)
    {
        if (is_array($data)) {
            foreach ($data as $productId => &$productData) {
                $productData[AbstractModifier::DATA_SOURCE_DEFAULT][ProductPermissionManager::PRODUCT_PERMISSIONS_KEY] =
                    $this->permissionManager->prepareAndLoadPermissions($productId);
            }
        }
        return $data;
    }
}
