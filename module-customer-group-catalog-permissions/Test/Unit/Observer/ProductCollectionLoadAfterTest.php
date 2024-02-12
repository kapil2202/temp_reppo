<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Observer;

use Aheadworks\CustGroupCatPermissions\Observer\ProductCollectionLoadAfter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event;

/**
 * Class ProductCollectionLoadAfterTest
 * Test for \Aheadworks\CustGroupCatPermissions\Observer\ProductCollectionLoadAfter
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Observer
 */
class ProductCollectionLoadAfterTest extends TestCase
{
    /**
     * @var ProductCollectionLoadAfter
     */
    private $observer;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

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

        $this->configMock = $this->getMockBuilder(Config::class)
                                 ->disableOriginalConstructor()
                                 ->setMethods([
                                    'isEnabled',
                                    'getProductBrowsingRedirectUrl'
                                ])->getMock();

        $this->permissionApplierMock = $this->createMock(Applier::class);

        $this->observer = $objectManager->getObject(
            ProductCollectionLoadAfter::class,
            [
                'config' => $this->configMock,
                'permissionApplier' => $this->permissionApplierMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecuteModuleDisabled()
    {
        $observerMock = $this->createMock(Observer::class);
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     */
    public function testExecuteApplyPermissions()
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $firstProductMock = $this->getMockForAbstractClass(ProductInterface::class);
        $secondProductMock = $this->getMockForAbstractClass(ProductInterface::class);

        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$firstProductMock, $secondProductMock]);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getCollection')
            ->willReturn($collectionMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->exactly(2))
            ->method('applyForProduct')
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }
}
