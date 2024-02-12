<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Plugin\Catalog\DataProvider\Product\Form;

use Aheadworks\CustGroupCatPermissions\Plugin\Catalog\DataProvider\Product\Form\ProductDataProviderPlugin;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider as ProductFormDataProvider;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermissionManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class ProductDataProviderPluginTest
 * Test for \Aheadworks\CustGroupCatPermissions\Plugin\Catalog\DataProvider\Product\Form\ProductDataProviderPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Plugin\Catalog\DataProvider\Product\Form
 */
class ProductDataProviderPluginTest extends TestCase
{
    /**
     * @var ProductDataProviderPlugin
     */
    private $plugin;

    /**
     * @var ProductPermissionManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->permissionManagerMock = $this->createPartialMock(
            ProductPermissionManager::class,
            [
                'prepareAndLoadPermissions'
            ]
        );

        $this->plugin = $objectManager->getObject(
            ProductDataProviderPlugin::class,
            [
                'permissionManager' => $this->permissionManagerMock
            ]
        );
    }

    /**
     * Test afterGetData method
     */
    public function testAfterGetData()
    {
        $productId = 10;
        $data = [
            $productId => [
                'entity_id' => $productId,
                'name' => '',
                'store_id' => 0,
            ]
        ];

        $permissions = [];
        $modifiedData = [
            $productId => [
                'entity_id' => $productId,
                'name' => '',
                'store_id' => 0,
                AbstractModifier::DATA_SOURCE_DEFAULT => [
                    ProductPermissionManager::PRODUCT_PERMISSIONS_KEY => $permissions
                ]
            ]
        ];

        $subjectMock = $this->createMock(ProductFormDataProvider::class);

        $this->permissionManagerMock->expects($this->exactly(count($data)))
            ->method('prepareAndLoadPermissions')
            ->with($productId)
            ->willReturn($permissions);

        $this->assertSame($modifiedData, $this->plugin->afterGetData($subjectMock, $data));
    }
}
