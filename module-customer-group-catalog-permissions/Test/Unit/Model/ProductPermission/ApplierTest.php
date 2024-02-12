<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission as PermissionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as CatalogSearchCollection;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Zend\Db\Sql\Select;

/**
 * Class ResolverTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission
 */
class ApplierTest extends TestCase
{
    /**
     * @var Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionResolverMock;

    /**
     * @var PermissionResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->permissionResolverMock = $this->createMock(Resolver::class);
        $this->resourceMock = $this->createMock(PermissionResource::class);
        $this->applier = $objectManager->getObject(
            Applier::class,
            [
                'resolver' => $this->permissionResolverMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Test applyForProduct method
     *
     * @param bool $isApplied
     * @param int $viewMode
     * @param int $priceMode
     * @param int $checkoutMode
     * @param string $priceMsg
     * @param string $buttonMsg
     * @dataProvider applyForProductProvider
     */
    public function testApplyForProduct($isApplied, $viewMode, $priceMode, $checkoutMode, $priceMsg, $buttonMsg)
    {
        $productMock = $this->createMock(Product::class);
        $permissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $callsCount = $isApplied ? 0 : 1;

        $productMock->expects($this->atLeastOnce())
            ->method('getData')
            ->with(Applier::PERMISSION_APPLIED)
            ->willReturn($isApplied);
        $productMock->expects($this->atLeast($callsCount))
            ->method('setData')
            ->willReturnSelf();
        $this->permissionResolverMock->expects($this->exactly($callsCount))
            ->method('getPermissionForProduct')
            ->with($productMock)
            ->willReturn($permissionMock);
        $permissionMock->expects($this->atLeast($callsCount))
            ->method('getViewMode')
            ->willReturn($viewMode);
        $permissionMock->expects($this->any())
            ->method('getPriceMode')
            ->willReturn($priceMode);
        $permissionMock->expects($this->any())
            ->method('getCheckoutMode')
            ->willReturn($checkoutMode);
        $permissionMock->expects($this->atMost($callsCount))
            ->method('getHiddenPriceMessage')
            ->willReturn($priceMsg);
        $permissionMock->expects($this->atMost($callsCount))
            ->method('getHiddenAddToCartMessage')
            ->willReturn($buttonMsg);

        $this->applier->applyForProduct($productMock);
    }

    /**
     * Test applyForCatalogSearchCollection method
     *
     * @param bool $isApplied
     * @param int $defaultViewMode
     * @dataProvider applyForCollectionProvider
     */
    public function testApplyForCatalogSearchCollection($isApplied, $defaultViewMode)
    {
        $collectionMock = $this->createMock(CatalogSearchCollection::class);
        $productIds = [1, 2];
        $fieldName = $defaultViewMode == AccessMode::SHOW ? Applier::NIN_ENTITY_ID : Applier::IN_ENTITY_ID;
        $storeId = 1;
        $customerGroupId = 1;
        $callsCount = $isApplied ? 0 : 1;

        $this->permissionResolverMock->expects($this->exactly($callsCount))
            ->method('getRequiredFilterValues')
            ->willReturn([$storeId, $customerGroupId, $defaultViewMode]);
        $this->resourceMock->expects($this->exactly($callsCount))
            ->method('getProductsFilterForSearch')
            ->with($storeId, $customerGroupId, $defaultViewMode)
            ->willReturn([$fieldName, $productIds]);
        $collectionMock->expects($this->exactly($callsCount))
            ->method('addFieldToFilter')
            ->with($fieldName, $productIds)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('getFlag')
            ->with(Applier::PERMISSION_APPLIED)
            ->willReturn($isApplied);
        $collectionMock->expects($this->exactly($callsCount))
            ->method('setFlag')
            ->with(Applier::PERMISSION_APPLIED, true)
            ->willReturnSelf();

        $this->applier->applyForCollection($collectionMock);
    }

    /**
     * Test applyForCatalogCollection method
     *
     * @param bool $isApplied
     * @param int $defaultViewMode
     * @dataProvider applyForCollectionProvider
     */
    public function testApplyForCatalogCollection($isApplied, $defaultViewMode)
    {
        $collectionMock = $this->createMock(AbstractCollection::class);
        $selectMock = $this->createMock(Select::class);
        $conditionType = $defaultViewMode == AccessMode::SHOW ? 'nin' : 'in';
        $filter = [$conditionType => $selectMock];
        $storeId = 1;
        $customerGroupId = 1;
        $callsCount = $isApplied ? 0 : 1;

        $this->permissionResolverMock->expects($this->exactly($callsCount))
            ->method('getRequiredFilterValues')
            ->willReturn([$storeId, $customerGroupId, $defaultViewMode]);
        $this->resourceMock->expects($this->exactly($callsCount))
            ->method('getProductsFilter')
            ->with($storeId, $customerGroupId, $defaultViewMode)
            ->willReturn($filter);
        $collectionMock->expects($this->exactly($callsCount))
            ->method('addFieldToFilter')
            ->with('entity_id', $filter)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('getFlag')
            ->with(Applier::PERMISSION_APPLIED)
            ->willReturn($isApplied);
        $collectionMock->expects($this->exactly($callsCount))
            ->method('setFlag')
            ->with(Applier::PERMISSION_APPLIED, true)
            ->willReturnSelf();

        $this->applier->applyForCollection($collectionMock);
    }

    /**
     * @return array
     */
    public function applyForProductProvider()
    {
        return [
            [false, AccessMode::SHOW, AccessMode::SHOW, AccessMode::SHOW, 'test', 'test'],
            [false, AccessMode::SHOW, AccessMode::SHOW, AccessMode::HIDE, 'test', 'test'],
            [false, AccessMode::SHOW, AccessMode::HIDE, AccessMode::HIDE, 'test', 'test'],
            [false, AccessMode::HIDE, AccessMode::HIDE, AccessMode::HIDE, 'test', 'test'],
            [true, AccessMode::SHOW, AccessMode::SHOW, AccessMode::SHOW, 'test', 'test']
        ];
    }

    /**
     * @return array
     */
    public function applyForCollectionProvider()
    {
        return [
            [false, AccessMode::SHOW],
            [true, AccessMode::HIDE]
        ];
    }
}
