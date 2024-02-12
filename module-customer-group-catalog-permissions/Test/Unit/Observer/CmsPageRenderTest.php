<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Observer;

use Aheadworks\CustGroupCatPermissions\Observer\CmsPageRender;
use Magento\Framework\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Cms\Model\Page;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Applier;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http as HttpResponse;

/**
 * Class CmsPageRenderTest
 * Test for \Aheadworks\CustGroupCatPermissions\Observer\CmsPageRender
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Observer
 */
class CmsPageRenderTest extends TestCase
{
    /**
     * @var CmsPageRender
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
                'getNoRouteCmsPageIdentifier',
                'getCmsPageBrowsingRedirectUrl',
            ]
        );

        $this->permissionApplierMock = $this->createMock(Applier::class);

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->observer = $objectManager->getObject(
            CmsPageRender::class,
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
    public function testExecuteNoNeedToApplyPermissions()
    {
        $noRouteCmsPageId = 'no_route_cms_page_id';
        $cmsPageId = $noRouteCmsPageId;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getNoRouteCmsPageIdentifier')
            ->willReturn($noRouteCmsPageId);

        $cmsPageMock = $this->createMock(Page::class);
        $cmsPageMock->expects($this->once())
            ->method('getIdentifier')
            ->willReturn($cmsPageId);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getPage')
            ->willReturn($cmsPageMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     */
    public function testExecuteApplyPermissions()
    {
        $noRouteCmsPageId = 'no_route_cms_page_id';
        $cmsPageId = 'cms_page_id';
        $isNeedToHidePage = false;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getNoRouteCmsPageIdentifier')
            ->willReturn($noRouteCmsPageId);

        $cmsPageMock = $this->createMock(Page::class);
        $cmsPageMock->expects($this->once())
            ->method('getIdentifier')
            ->willReturn($cmsPageId);
        $cmsPageMock->expects($this->once())
            ->method('getData')
            ->with(Applier::HIDE_PAGE)
            ->willReturn($isNeedToHidePage);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getPage')
            ->willReturn($cmsPageMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCmsPage')
            ->with($cmsPageMock)
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     * @expectedExceptionMessage Sorry, you are not allowed to view this page.
     */
    public function testExecuteApplyPermissionsAndPerformRedirect()
    {
        $noRouteCmsPageId = 'no_route_cms_page_id';
        $cmsPageId = 'cms_page_id';
        $isNeedToHidePage = true;
        $cmsPageBrowsingRedirectUrl = 'https://www.test.com/';
        $requestPathInfo = 'request_path_info';

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getNoRouteCmsPageIdentifier')
            ->willReturn($noRouteCmsPageId);
        $this->configMock->expects($this->once())
            ->method('getCmsPageBrowsingRedirectUrl')
            ->willReturn($cmsPageBrowsingRedirectUrl);

        $cmsPageMock = $this->createMock(Page::class);
        $cmsPageMock->expects($this->once())
            ->method('getIdentifier')
            ->willReturn($cmsPageId);
        $cmsPageMock->expects($this->once())
            ->method('getData')
            ->with(Applier::HIDE_PAGE)
            ->willReturn($isNeedToHidePage);

        $responseMock = $this->createMock(HttpResponse::class);
        $responseMock->expects($this->once())
            ->method('setRedirect')
            ->with($cmsPageBrowsingRedirectUrl)
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

        $requestMock = $this->createMock(HttpRequest::class);
        $requestMock->expects($this->once())
            ->method('getPathInfo')
            ->willReturn($requestPathInfo);
        $requestMock->expects($this->once())
            ->method('setPathInfo')
            ->with($requestPathInfo . CmsPageRender::REDIRECTED_FLAG)
            ->willReturn($requestPathInfo);

        $eventMock = $this->getMockBuilder(Event::class)
                          ->disableOriginalConstructor()
                          ->setMethods([
                                'getPage',
                                'getRequest',
                                'getControllerAction'
                          ])->getMock();
        $eventMock->expects($this->once())
            ->method('getPage')
            ->willReturn($cmsPageMock);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);
        $eventMock->expects($this->once())
            ->method('getControllerAction')
            ->willReturn($controllerActionMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->exactly(3))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCmsPage')
            ->with($cmsPageMock)
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Sorry, you are not allowed to view this page.'))
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     * @expectedException \Magento\Framework\Exception\NotFoundException
     * @expectedExceptionMessage Sorry, you are not allowed to view this page.
     */
    public function testExecuteApplyPermissionsAndPerformRedirectWithoutUrl()
    {
        $noRouteCmsPageId = 'no_route_cms_page_id';
        $cmsPageId = 'cms_page_id';
        $isNeedToHidePage = true;
        $cmsPageBrowsingRedirectUrl = null;
        $requestPathInfo = 'request_path_info';

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->configMock->expects($this->once())
            ->method('getNoRouteCmsPageIdentifier')
            ->willReturn($noRouteCmsPageId);
        $this->configMock->expects($this->once())
            ->method('getCmsPageBrowsingRedirectUrl')
            ->willReturn($cmsPageBrowsingRedirectUrl);

        $cmsPageMock = $this->createMock(Page::class);
        $cmsPageMock->expects($this->once())
            ->method('getIdentifier')
            ->willReturn($cmsPageId);
        $cmsPageMock->expects($this->once())
            ->method('getData')
            ->with(Applier::HIDE_PAGE)
            ->willReturn($isNeedToHidePage);

        $responseMock = $this->createMock(HttpResponse::class);
        $responseMock->expects($this->never())
            ->method('setRedirect')
            ->with($cmsPageBrowsingRedirectUrl)
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
        $controllerActionMock->expects($this->never())
            ->method('getResponse')
            ->willReturn($responseMock);

        $requestMock = $this->createMock(HttpRequest::class);
        $requestMock->expects($this->never())
            ->method('getPathInfo')
            ->willReturn($requestPathInfo);
        $requestMock->expects($this->never())
            ->method('setPathInfo')
            ->with($requestPathInfo . CmsPageRender::REDIRECTED_FLAG)
            ->willReturn($requestPathInfo);

        $eventMock = $this->getMockBuilder(Event::class)
                          ->disableOriginalConstructor()
                          ->setMethods([
                              'getPage',
                              'getRequest',
                              'getControllerAction'
                          ])->getMock();
        $eventMock->expects($this->once())
            ->method('getPage')
            ->willReturn($cmsPageMock);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);
        $eventMock->expects($this->never())
            ->method('getControllerAction')
            ->willReturn($controllerActionMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForCmsPage')
            ->with($cmsPageMock)
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage')
            ->with(__('Sorry, you are not allowed to view this page.'))
            ->willReturnSelf();
        $this->expectException(NotFoundException::class);
        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }
}
