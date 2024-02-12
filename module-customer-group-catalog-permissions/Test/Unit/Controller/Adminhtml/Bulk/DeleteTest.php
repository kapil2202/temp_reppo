<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Controller\Adminhtml\Bulk;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk\Delete;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;

/**
 * Class DeleteTest
 * Test for \Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk\Delete
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Controller\Adminhtml\Bulk
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);

        $this->controller = $objectManager->getObject(
            Delete::class,
            [
                'context' => $this->contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $titleMock = $this->createMock(Title::class);

        $pageConfigMock = $this->createMock(Config::class);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);

        $resultPageMock = $this->createMock(Page::class);
        $resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->willReturnSelf();
        $resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }
}
