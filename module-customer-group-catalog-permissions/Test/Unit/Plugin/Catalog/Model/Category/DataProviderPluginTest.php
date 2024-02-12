<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Plugin\Catalog\Model\Category;

use Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Category\DataProviderPlugin;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermissionManager;

/**
 * Class DataProviderPluginTest
 * Test for \Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Category\DataProviderPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Plugin\Catalog\Model\Category
 */
class DataProviderPluginTest extends TestCase
{
    /**
     * @var DataProviderPlugin
     */
    private $plugin;

    /**
     * @var CategoryPermissionManager|\PHPUnit_Framework_MockObject_MockObject
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
            CategoryPermissionManager::class,
            [
                'prepareAndLoadPermissions'
            ]
        );

        $this->plugin = $objectManager->getObject(
            DataProviderPlugin::class,
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
        $categoryId = 10;
        $data = [
            $categoryId => [
                'entity_id' => $categoryId,
                'name' => '',
                'store_id' => 0,
            ]
        ];

        $permissions = [];
        $modifiedData = [
            $categoryId => [
                'entity_id' => $categoryId,
                'name' => '',
                'store_id' => 0,
                CategoryPermissionManager::CATEGORY_PERMISSIONS_KEY => $permissions
            ]
        ];

        $subjectMock = $this->createMock(CategoryDataProvider::class);

        $this->permissionManagerMock->expects($this->exactly(count($data)))
            ->method('prepareAndLoadPermissions')
            ->with($categoryId)
            ->willReturn($permissions);

        $this->assertSame($modifiedData, $this->plugin->afterGetData($subjectMock, $data));
    }
}
