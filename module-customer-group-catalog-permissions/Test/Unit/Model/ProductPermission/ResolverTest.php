<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\ProductPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Converter;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver;
use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Resolver as CategoryPermissionResolver;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Aheadworks\CustGroupCatPermissions\Model\Source\Config\AccessMode as ConfigAccessSource;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver\CurrentCategory as CurrentCategoryResolver;

/**
 * Class ResolverTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission
 */
class ResolverTest extends TestCase
{
    /**
     * @var ProductPermissionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productPermissionRepositoryMock;

    /**
     * @var Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionConverterMock;

    /**
     * @var CategoryPermissionResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionResolverMock;

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
     * @var CurrentCategoryResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $currentCategoryResolverMock;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $messages = [
        ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => 'Hidden price message',
        ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => 'Hidden add to cart message'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->productPermissionRepositoryMock =
            $this->getMockForAbstractClass(ProductPermissionRepositoryInterface::class);
        $this->categoryPermissionResolverMock = $this->createMock(CategoryPermissionResolver::class);
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
        $this->currentCategoryResolverMock = $this->createMock(CurrentCategoryResolver::class);

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'config' => $this->configMock,
                'storeManager' => $this->storeManagerMock,
                'httpContext' => $this->httpContextMock,
                'productPermissionRepository' => $this->productPermissionRepositoryMock,
                'converter' => $this->permissionConverterMock,
                'resolver' => $this->categoryPermissionResolverMock,
                'session' => $this->sessionMock,
                'currentCategoryResolver' => $this->currentCategoryResolverMock
            ]
        );
    }

    /**
     * Test getPermissionForProduct method
     *
     * @param bool $throwException
     * @param ProductPermissionInterface|\PHPUnit_Framework_MockObject_MockObject $permissionMock
     * @dataProvider getPermissionForProductProvider
     */
    public function testGetPermissionForProduct($throwException, $permissionMock)
    {
        $productMock = $this->createMock(Product::class);
        $categoryMock = $this->createMock(Category::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $exception = new NoSuchEntityException(__('No such entity!'));
        $callsCount = $throwException ? 0 : 1;
        $categoryResolverCallsCount = $throwException ? 1 : 0;
        $productId = 1;
        $storeId = 1;
        $customerGroupId = 1;

        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($productId);
        $this->currentCategoryResolverMock->expects($this->once())
            ->method('getCurrentCategory')
            ->with($productMock)
            ->willReturn($categoryMock);
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
            $this->productPermissionRepositoryMock->expects($this->once())
                ->method('getForProduct')
                ->with($productId, $storeId, $customerGroupId)
                ->willThrowException($exception);
        } else {
            $this->productPermissionRepositoryMock->expects($this->once())
                ->method('getForProduct')
                ->with($productId, $storeId, $customerGroupId)
                ->willReturn($permissionMock);
        }
        $this->categoryPermissionResolverMock->expects($this->exactly($categoryResolverCallsCount))
            ->method('getPermissionForCategory')
            ->with($categoryMock)
            ->willReturn($permissionMock);
        $permissionMock->expects($this->exactly($callsCount))
            ->method('getHiddenPriceMessage')
            ->willReturn(null);
        $permissionMock->expects($this->exactly($callsCount))
            ->method('setHiddenPriceMessage')
            ->with($this->messages[ProductPermissionInterface::HIDDEN_PRICE_MESSAGE])
            ->willReturnSelf();
        $permissionMock->expects($this->exactly($callsCount))
            ->method('getHiddenAddToCartMessage')
            ->willReturn(null);
        $permissionMock->expects($this->exactly($callsCount))
            ->method('setHiddenAddToCartMessage')
            ->with($this->messages[ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE])
            ->willReturnSelf();
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementProductPriceDisplayingMessageForHiddenPrice')
            ->with($storeId)
            ->willReturn($this->messages[ProductPermissionInterface::HIDDEN_PRICE_MESSAGE]);
        $this->configMock->expects($this->exactly($callsCount))
            ->method('getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton')
            ->with($storeId)
            ->willReturn($this->messages[ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE]);

        $this->assertSame($permissionMock, $this->resolver->getPermissionForProduct($productMock));
    }

    /**
     * Test getPermissionForProduct method with return default permissions
     */
    public function testGetPermissionForProductDefault()
    {
        $permissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $productMock = $this->createMock(Product::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $exception = new NoSuchEntityException(__('No such entity!'));
        $productId = 1;
        $storeId = 1;
        $customerGroupId = 1;
        $permissionData = [
            ProductPermissionInterface::VIEW_MODE => 2,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE =>
                $this->messages[ProductPermissionInterface::HIDDEN_PRICE_MESSAGE],
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE =>
                $this->messages[ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE],
            CategoryPermissionInterface::PERMISSION_TYPE => CategoryPermissionInterface::DEFAULT_PERMISSION_TYPE
        ];

        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($productId);
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
        $this->productPermissionRepositoryMock->expects($this->once())
            ->method('getForProduct')
            ->with($productId, $storeId, $customerGroupId)
            ->willThrowException($exception);
        $this->categoryPermissionResolverMock->expects($this->never())
            ->method('getPermissionForCategory');
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductPriceDisplayingMessageForHiddenPrice')
            ->with($storeId)
            ->willReturn($this->messages[ProductPermissionInterface::HIDDEN_PRICE_MESSAGE]);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductAddToCartButtonDisplayingMessageForHiddenButton')
            ->with($storeId)
            ->willReturn($this->messages[ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE]);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementBrowsingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementBrowsingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductPriceDisplayingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductPriceDisplayingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductAddToCartButtonDisplayingMode')
            ->with($storeId)
            ->willReturn(ConfigAccessSource::HIDE_FROM_EVERYONE);
        $this->configMock->expects($this->once())
            ->method('getCatalogElementProductAddToCartButtonDisplayingCustomerGroups')
            ->with($storeId)
            ->willReturn([$customerGroupId]);
        $this->permissionConverterMock->expects($this->once())
            ->method('getDataObject')
            ->with($permissionData)
            ->willReturn($permissionMock);

        $this->assertSame($permissionMock, $this->resolver->getPermissionForProduct($productMock));
    }

    /**
     * @return array
     */
    public function getPermissionForProductProvider()
    {
        $productPermissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $categoryPermissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);

        return [
            [false, $productPermissionMock],
            [true, $categoryPermissionMock]
        ];
    }
}
