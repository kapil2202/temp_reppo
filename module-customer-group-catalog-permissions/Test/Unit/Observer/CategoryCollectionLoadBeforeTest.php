<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Observer;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Observer\CategoryCollectionLoadBefore;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;

/**
 * Class CategoryCollectionLoadBeforeTest
 * Test for \Aheadworks\CustGroupCatPermissions\Observer\CategoryCollectionLoadBefore
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Observer
 */
class CategoryCollectionLoadBeforeTest extends TestCase
{
    /**
     * @var CategoryCollectionLoadBefore
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

        $this->configMock = $this->createMock(Config::class);
        $this->permissionApplierMock = $this->createMock(Applier::class);

        $this->observer = $objectManager->getObject(
            CategoryCollectionLoadBefore::class,
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
    public function testExecute()
    {
        $categoryCollectionMock = $this->getMockForAbstractClass(
            AbstractCollection::class,
            [],
            '',
            false
        );

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getCategoryCollection')
            ->willReturn($categoryCollectionMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCollection')
            ->with($categoryCollectionMock)
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }
}
