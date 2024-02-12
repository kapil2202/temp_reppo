<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Observer;

use Aheadworks\CustGroupCatPermissions\Observer\ControllerProductInitAfter;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Event;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Catalog\Model\Product;

/**
 * Class ControllerProductInitAfterTest
 * Test for \Aheadworks\CustGroupCatPermissions\Observer\ControllerProductInitAfter
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Observer
 */
class ControllerProductInitAfterTest extends TestCase
{
    /**
     * @var ControllerProductInitAfter
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
            ControllerProductInitAfter::class,
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
        $isNeedToHideProduct = false;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getData')
            ->with(Applier::HIDE_PRODUCT)
            ->willReturn($isNeedToHideProduct);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getProduct')
            ->willReturn($productMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForProduct')
            ->with($productMock)
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Sorry, you are not allowed to view this product.
     */
    public function testExecuteApplyPermissionsWithoutUrl()
    {
        $isNeedToHideProduct = true;
        $productBrowsingRedirectUrl = null;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementBrowsingRedirectUrl')
            ->willReturn($productBrowsingRedirectUrl);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getData')
            ->with(Applier::HIDE_PRODUCT)
            ->willReturn($isNeedToHideProduct);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getProduct')
            ->willReturn($productMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForProduct')
            ->with($productMock)
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Sorry, you are not allowed to view this product.'))
            ->willReturnSelf();
        $this->expectException(LocalizedException::class);
        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Sorry, you are not allowed to view this product.
     */
    public function testExecuteRedirect()
    {
        $isNeedToHideProduct = true;
        $productBrowsingRedirectUrl = 'https://www.test.com/';

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementBrowsingRedirectUrl')
            ->willReturn($productBrowsingRedirectUrl);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getData')
            ->with(Applier::HIDE_PRODUCT)
            ->willReturn($isNeedToHideProduct);

        $responseMock = $this->createMock(HttpResponse::class);
        $responseMock->expects($this->once())
            ->method('setRedirect')
            ->with($productBrowsingRedirectUrl)
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
                                'getProduct',
                                'getControllerAction'
                            ])->getMock();
        $eventMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);
        $eventMock->expects($this->once())
            ->method('getControllerAction')
            ->willReturn($controllerActionMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForProduct')
            ->with($productMock)
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Sorry, you are not allowed to view this product.'))
            ->willReturnSelf();
        $this->expectException(LocalizedException::class);
        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }
}
