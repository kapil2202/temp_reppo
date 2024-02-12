<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\CmsPagePermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Converter;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Resolver;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Customer\Model\Context as CustomerContext;
use Aheadworks\CustGroupCatPermissions\Model\Source\Config\AccessMode as ConfigAccessSource;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ResolverTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CmsPagePermission
 */
class ResolverTest extends TestCase
{
    /**
     * @var CmsPagePermissionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPagePermissionRepositoryMock;

    /**
     * @var Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionConverterMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->cmsPagePermissionRepositoryMock =
            $this->getMockForAbstractClass(CmsPagePermissionRepositoryInterface::class);
        $this->permissionConverterMock = $this->createMock(Converter::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->httpContextMock = $this->createMock(HttpContext::class);
        $this->sessionMock = $this->createMock(Session::class);
        $this->configMock = $this->createPartialMock(
            Config::class,
            [
                'getCmsPageBrowsingMode',
                'getCmsPageBrowsingCustomerGroups'
            ]
        );

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'config' => $this->configMock,
                'storeManager' => $this->storeManagerMock,
                'httpContext' => $this->httpContextMock,
                'cmsPagePermissionRepository' => $this->cmsPagePermissionRepositoryMock,
                'converter' => $this->permissionConverterMock,
                'session' => $this->sessionMock
            ]
        );
    }

    /**
     * Test getPermissionForCmsPage method
     *
     * @param bool $throwException
     * @dataProvider getPermissionForCmsPageProvider
     */
    public function testGetPermissionForCmsPage($throwException)
    {
        $permissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $cmsPageMock = $this->createMock(CmsPage::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $exception = new NoSuchEntityException(__('No such entity!'));
        $callsCount = $throwException ? 1 : 0;
        $cmsPageId = 3;
        $storeId = 1;
        $customerGroupId = 1;
        $permissionData = [CmsPagePermissionInterface::VIEW_MODE => 2];

        $cmsPageMock->expects($this->once())
            ->method('getId')
            ->willReturn($cmsPageId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->httpContextMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_GROUP)
            ->willReturn($customerGroupId);
        $this->sessionMock->expects($this->never())
            ->method('getCustomerGroupId');
        if ($throwException) {
            $this->cmsPagePermissionRepositoryMock->expects($this->once())
                ->method('getForCmsPage')
                ->with($cmsPageId, $storeId, $customerGroupId)
                ->willThrowException($exception);
        } else {
            $this->cmsPagePermissionRepositoryMock->expects($this->once())
                ->method('getForCmsPage')
                ->with($cmsPageId, $storeId, $customerGroupId)
                ->willReturn($permissionMock);
        }
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCmsPageBrowsingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCmsPageBrowsingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->permissionConverterMock->expects($this->exactly($callsCount))
            ->method('getDataObject')
            ->with($permissionData)
            ->willReturn($permissionMock);

        $this->assertSame($permissionMock, $this->resolver->getPermissionForCmsPage($cmsPageMock));
    }

    /**
     * @return array
     */
    public function getPermissionForCmsPageProvider()
    {
        return [
            [false],
            [true]
        ];
    }
}
