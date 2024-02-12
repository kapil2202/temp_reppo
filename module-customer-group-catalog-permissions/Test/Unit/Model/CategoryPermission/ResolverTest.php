<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\CategoryPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Converter;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Resolver;
use Magento\Catalog\Model\Category;
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
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CategoryPermission
 */
class ResolverTest extends TestCase
{
    /**
     * @var CategoryPermissionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionRepositoryMock;

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
     * @var array
     */
    private $messages = [
        CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => 'Hidden price message',
        CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => 'Hidden add to cart message'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->categoryPermissionRepositoryMock =
            $this->getMockForAbstractClass(CategoryPermissionRepositoryInterface::class);
        $this->permissionConverterMock = $this->createMock(Converter::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->httpContextMock = $this->createMock(HttpContext::class);
        $this->sessionMock = $this->createMock(Session::class);
        $this->configMock = $this->createPartialMock(
            Config::class,
            [
                'getCatalogElementProductPriceDisplayingMessageForHiddenPrice',
                'getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton',
                'getCatalogElementBrowsingMode',
                'getCatalogElementBrowsingCustomerGroups',
                'getCatalogElementProductPriceDisplayingMode',
                'getCatalogElementProductPriceDisplayingCustomerGroups',
                'getCatalogElementProductAddToCartButtonDisplayingMode',
                'getCatalogElementProductAddToCartButtonDisplayingCustomerGroups'
            ]
        );

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'config' => $this->configMock,
                'storeManager' => $this->storeManagerMock,
                'httpContext' => $this->httpContextMock,
                'categoryPermissionRepository' => $this->categoryPermissionRepositoryMock,
                'converter' => $this->permissionConverterMock,
                'session' => $this->sessionMock
            ]
        );
    }

    /**
     * Test getPermissionForCategory method
     *
     * @param bool $throwException
     * @dataProvider getPermissionForCategoryProvider
     */
    public function testGetPermissionForCategory($throwException)
    {
        $permissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $categoryMock = $this->createMock(Category::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $exception = new NoSuchEntityException(__('No such entity!'));
        $pathIds = [1 , 2, 3];
        $permissionsGettersCallsCount = $throwException ? 0 : 1;
        $callsCount = $throwException ? 1 : 0;
        $categoryId = 3;
        $storeId = 1;
        $customerGroupId = 1;
        $permissionData = [
            CategoryPermissionInterface::VIEW_MODE => 2,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE =>
                $this->messages[CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE],
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE =>
                $this->messages[CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE],
            CategoryPermissionInterface::PERMISSION_TYPE => CategoryPermissionInterface::DEFAULT_PERMISSION_TYPE
        ];

        $categoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($categoryId);
        $categoryMock->expects($this->once())
            ->method('getPathIds')
            ->willReturn($pathIds);
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
            $this->categoryPermissionRepositoryMock->expects($this->once())
                ->method('getForCategory')
                ->with($categoryId, $pathIds, $storeId, $customerGroupId)
                ->willThrowException($exception);
        } else {
            $this->categoryPermissionRepositoryMock->expects($this->once())
                ->method('getForCategory')
                ->with($categoryId, $pathIds, $storeId, $customerGroupId)
                ->willReturn($permissionMock);
        }
        $permissionMock->expects($this->exactly($permissionsGettersCallsCount))
            ->method('getHiddenPriceMessage')
            ->willReturn(null);
        $permissionMock->expects($this->exactly($permissionsGettersCallsCount))
            ->method('setHiddenPriceMessage')
            ->with($this->messages[CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE])
            ->willReturnSelf();
        $permissionMock->expects($this->exactly($permissionsGettersCallsCount))
            ->method('getHiddenAddToCartMessage')
            ->willReturn(null);
        $permissionMock->expects($this->exactly($permissionsGettersCallsCount))
            ->method('setHiddenAddToCartMessage')
            ->with($this->messages[CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE])
            ->willReturnSelf();
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductPriceDisplayingMessageForHiddenPrice')
            ->with($storeId)
            ->willReturn($this->messages[CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE]);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton')
            ->with($storeId)
            ->willReturn($this->messages[CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE]);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementBrowsingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementBrowsingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementProductPriceDisplayingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementProductPriceDisplayingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementProductAddToCartButtonDisplayingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementProductAddToCartButtonDisplayingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->permissionConverterMock->expects($this->exactly($callsCount))
            ->method('getDataObject')
            ->with($permissionData)
            ->willReturn($permissionMock);

        $this->assertSame($permissionMock, $this->resolver->getPermissionForCategory($categoryMock));
    }

    /**
     * @return array
     */
    public function getPermissionForCategoryProvider()
    {
        return [
            [false],
            [true]
        ];
    }
}
