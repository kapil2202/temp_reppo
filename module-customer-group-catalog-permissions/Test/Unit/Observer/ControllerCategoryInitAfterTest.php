<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Observer;

use Aheadworks\CustGroupCatPermissions\Observer\ControllerCategoryInitAfter;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Event;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http as HttpResponse;

/**
 * Class ControllerCategoryInitAfterTest
 * Test for \Aheadworks\CustGroupCatPermissions\Observer\ControllerCategoryInitAfter
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Observer
 */
class ControllerCategoryInitAfterTest extends TestCase
{
    /**
     * @var ControllerCategoryInitAfter
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
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

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
                'isEnabled',
                'getCatalogElementBrowsingRedirectUrl'
            ]
        );

        $this->permissionApplierMock = $this->createMock(Applier::class);

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->observer = $objectManager->getObject(
            ControllerCategoryInitAfter::class,
            [
                'config' => $this->configMock,
                'permissionApplier' => $this->permissionApplierMock,
                'messageManager' => $this->messageManagerMock
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
    public function testExecuteApplyPermissionsNoRedirect()
    {
        $isCategoryActive = true;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($isCategoryActive);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getCategory')
            ->willReturn($categoryMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCategory')
            ->with($categoryMock)
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testExecuteApplyPermissionsWithoutUrl()
    {
        $isCategoryActive = false;
        $categoryBrowsingRedirectUrl = null;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementBrowsingRedirectUrl')
            ->willReturn($categoryBrowsingRedirectUrl);

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($isCategoryActive);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getCategory')
            ->willReturn($categoryMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCategory')
            ->with($categoryMock)
            ->willReturnSelf();

        $this->expectException(LocalizedException::class);
        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testExecuteRedirect()
    {
        $isCategoryActive = false;
        $categoryBrowsingRedirectUrl = 'https://www.test.com/';

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementBrowsingRedirectUrl')
            ->willReturn($categoryBrowsingRedirectUrl);

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($isCategoryActive);

        $responseMock = $this->createMock(HttpResponse::class);
        $responseMock->expects($this->once())
            ->method('setRedirect')
            ->with($categoryBrowsingRedirectUrl)
            ->willReturnSelf();

        $controllerActionMock = $this->getMockForAbstractClass(
            Action::class,
            [],
            '',
            false,
            true,
            true,
            ['getResponse']
        );
        $controllerActionMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $eventMock = $this->getMockBuilder(Event::class)
                          ->disableOriginalConstructor()
                          ->setMethods([
                                'getCategory',
                                'getControllerAction'
                            ])->getMock();
        $eventMock->expects($this->once())
            ->method('getCategory')
            ->willReturn($categoryMock);
        $eventMock->expects($this->once())
            ->method('getControllerAction')
            ->willReturn($controllerActionMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCategory')
            ->with($categoryMock)
            ->willReturnSelf();

        $this->expectException(LocalizedException::class);
        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }
}
