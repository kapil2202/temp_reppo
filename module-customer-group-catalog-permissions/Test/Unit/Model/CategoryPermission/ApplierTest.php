<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Resolver;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission as PermissionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Catalog\Model\Category;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Zend\Db\Sql\Select;

/**
 * Class ResolverTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CategoryPermission
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
     * Test applyForCategory method
     *
     * @param bool $isApplied
     * @param int $viewMode
     * @param int $priceMode
     * @dataProvider applyForCategoryProvider
     */
    public function testApplyForCategory($isApplied, $viewMode, $priceMode)
    {
        $categoryMock = $this->createMock(Category::class);
        $permissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $callsCount = $isApplied ? 0 : 1;

        $categoryMock->expects($this->atLeastOnce())
            ->method('getData')
            ->with(Applier::PERMISSION_APPLIED)
            ->willReturn($isApplied);
        $categoryMock->expects($this->atLeast($callsCount))
            ->method('setData')
            ->willReturnSelf();
        $this->permissionResolverMock->expects($this->exactly($callsCount))
            ->method('getPermissionForCategory')
            ->with($categoryMock)
            ->willReturn($permissionMock);
        $permissionMock->expects($this->atLeast($callsCount))
            ->method('getViewMode')
            ->willReturn($viewMode);
        $permissionMock->expects($this->atLeast($callsCount))
            ->method('getPriceMode')
            ->willReturn($priceMode);

        $this->applier->applyForCategory($categoryMock);
    }

    /**
     * Test applyForCollection method
     *
     * @param bool $isApplied
     * @param int $defaultViewMode
     * @dataProvider applyForCollectionProvider
     */
    public function testApplyForCollection($isApplied, $defaultViewMode)
    {
        $collectionMock = $this->createMock(AbstractCollection::class);
        $selectMock = $this->createMock(Select::class);
        $conditionType = $defaultViewMode == AccessMode::SHOW ? 'nin' : 'in';
        $filter = [$conditionType => $selectMock];
        $storeId = 1;
        $customerGroupId = 1;
        $callsCount = $isApplied ? 0 : 1;
        $callsFilterCount = $isApplied ? 0 : 2;

        $this->permissionResolverMock->expects($this->exactly($callsCount))
            ->method('getRequiredFilterValues')
            ->willReturn([$storeId, $customerGroupId, $defaultViewMode]);
        $this->resourceMock->expects($this->exactly($callsCount))
            ->method('getCategoriesFilter')
            ->with($storeId, $customerGroupId, $defaultViewMode)
            ->willReturn($filter);
        $collectionMock->expects($this->exactly($callsFilterCount))
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
    public function applyForCategoryProvider()
    {
        return [
            [false, AccessMode::SHOW, AccessMode::SHOW],
            [false, AccessMode::SHOW, AccessMode::HIDE],
            [false, AccessMode::HIDE, AccessMode::HIDE],
            [true, AccessMode::SHOW, AccessMode::SHOW]
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
