<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\Service;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\CustGroupCatPermissions\Model\Service\ResponseManager as ResponseManager;
use Magento\Framework\App\Response\RedirectInterface;

/**
 * Class ResponseManagerTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\Service
 */
class ResponseManagerTest extends TestCase
{
    /**
     * @var RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $redirectMock;

    /**
     * @var object
     */
    private $responseManager;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->redirectMock = $this->getMockForAbstractClass(RedirectInterface::class);
        $this->responseManager = $objectManager->getObject(
            ResponseManager::class,
            [
                'redirect' => $this->redirectMock
            ]
        );
    }

    /**
     * Test getCurrentRedirectRefererUrlKey method
     *
     * @param string $refererUrl
     * @param string $urlKey
     * @dataProvider getCurrentRedirectRefererUrlKeyProvider
     */
    public function testGetCurrentRedirectRefererUrlKey($refererUrl, $urlKey)
    {
        $this->redirectMock->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn($refererUrl);

        $this->assertEquals($urlKey, $this->responseManager->getCurrentRedirectRefererUrlKey());
    }

    /**
     * @return array
     */
    public function getCurrentRedirectRefererUrlKeyProvider()
    {
        return [
            ['http://domen.com/test-category.html', 'test-category'],
            ['', '']
        ];
    }
}
