<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Plugin\Catalog\Model\Category;

use Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Layer\FilterListPlugin;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterList as LayerFilterList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;

/**
 * Class FilterListPluginTest
 * Test for \Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Layer\FilterListPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Plugin\Catalog\Model\Category
 */
class FilterListPluginTest extends TestCase
{
    /**
     * @var FilterListPlugin
     */
    private $plugin;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registryMock;

    /**
     * @var Applier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionApplierMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createPartialMock(
            Config::class,
            [
                'isEnabled'
            ]
        );

        $this->registryMock = $this->createPartialMock(
            Registry::class,
            [
                'registry'
            ]
        );

        $this->permissionApplierMock = $this->createPartialMock(
            Applier::class,
            [
                'applyForCategory'
            ]
        );

        $this->plugin = $objectManager->getObject(
            FilterListPlugin::class,
            [
                'config' => $this->configMock,
                'registry' => $this->registryMock,
                'permissionApplier' => $this->permissionApplierMock
            ]
        );
    }

    /**
     * Test afterGetFilters method
     */
    public function testAfterGetFiltersModuleDisabled()
    {
        $result = [];
        $subjectMock = $this->createMock(LayerFilterList::class);
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->assertSame($result, $this->plugin->afterGetFilters($subjectMock, $result));
    }
}
