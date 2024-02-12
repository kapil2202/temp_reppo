<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model;

use Aheadworks\CustGroupCatPermissions\Api\ProductPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Converter as PermissionsConverter;
use Aheadworks\CustGroupCatPermissions\Model\PostPermissionDataProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermissionManager;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermissionRepository;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ProductPermissionManagerTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model
 */
class ProductPermissionManagerTest extends TestCase
{
    /**
     * @var ProductPermissionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productPermissionRepositoryMock;

    /**
     * @var PermissionsConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionsConverterMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var PostPermissionDataProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postPermissionDataProcessorMock;

    /**
     * @var ProductPermissionManager
     */
    private $permissionManager;

    /**
     * @var int
     */
    private $productId = 3;

    /**
     * @var int
     */
    private $permissionObjectsCount = 6;

    /**
     * @var array
     */
    private $permissionsData = [
        [
            PermissionsConverter::STORE_IDS => [1],
            PermissionsConverter::CUSTOMER_GROUP_IDS => [1, 2],
            ProductPermissionInterface::RECORD_ID => 1,
            ProductPermissionInterface::PRODUCT_ID => 2,
            ProductPermissionInterface::VIEW_MODE => 1,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2
        ],
        [
            PermissionsConverter::STORE_IDS => [2],
            PermissionsConverter::CUSTOMER_GROUP_IDS => [1, 2],
            ProductPermissionInterface::RECORD_ID => 2,
            ProductPermissionInterface::PRODUCT_ID => 2,
            ProductPermissionInterface::VIEW_MODE => 1,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2
        ]
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->productPermissionRepositoryMock = $this->createPartialMock(
            ProductPermissionRepository::class,
            [
                'save',
                'getList',
                'deleteForProducts'
            ]
        );
        $this->permissionsConverterMock = $this->createPartialMock(
            PermissionsConverter::class,
            [
                'convertPermissionsToSave',
                'convertPermissionsToDisplay'
            ]
        );
        $this->postPermissionDataProcessorMock =
            $this->createMock(PostPermissionDataProcessor::class);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            [
                'addFilter',
                'create'
            ]
        );
        $this->permissionManager = $objectManager->getObject(
            ProductPermissionManager::class,
            [
                'productPermissionRepository' => $this->productPermissionRepositoryMock,
                'permissionsConverter' => $this->permissionsConverterMock,
                'postPermissionDataProcessor' => $this->postPermissionDataProcessorMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test prepareAndSavePermissions method
     */
    public function testPrepareAndSavePermissions()
    {
        $permissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);

        $this->postPermissionDataProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($this->permissionsData)
            ->willReturn($this->permissionsData);
        $this->permissionsConverterMock->expects($this->once())
            ->method('convertPermissionsToSave')
            ->with($this->permissionsData, $this->productId)
            ->willReturn($permissionObjects);
        $this->productPermissionRepositoryMock->expects($this->once())
            ->method('deleteForProducts')
            ->with([$this->productId]);
        $this->productPermissionRepositoryMock->expects($this->exactly($this->permissionObjectsCount))
            ->method('save')
            ->with($permissionMock)
            ->willReturn($permissionMock);

        $this->permissionManager->prepareAndSavePermissions($this->permissionsData, $this->productId);
    }

    /**
     * Test prepareAndSavePermissions method with exception
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Something went wrong while saving permissions.
     */
    public function testPrepareAndSavePermissionsWithException()
    {
        $permissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);
        $exception = new CouldNotSaveException(__('Test message!'));

        $this->postPermissionDataProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($this->permissionsData)
            ->willReturn($this->permissionsData);
        $this->permissionsConverterMock->expects($this->once())
            ->method('convertPermissionsToSave')
            ->with($this->permissionsData, $this->productId)
            ->willReturn($permissionObjects);
        $this->productPermissionRepositoryMock->expects($this->once())
            ->method('deleteForProducts')
            ->with([$this->productId]);
        $this->productPermissionRepositoryMock->expects($this->once())
            ->method('save')
            ->with($permissionMock)
            ->willThrowException($exception);
        $this->expectException(\Exception::class);
        $this->permissionManager->prepareAndSavePermissions($this->permissionsData, $this->productId);
    }

    /**
     * Test prepareAndLoadPermissions method
     *
     * @param array $permissionObjects
     * @dataProvider prepareAndLoadPermissionsProvider
     */
    public function testPrepareAndLoadPermissions($permissionObjects)
    {
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(ProductPermissionSearchResultsInterface::class);
        $callsCount = empty($permissionObjects) ? 0 : 1;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(ProductPermissionInterface::PRODUCT_ID, $this->productId)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->productPermissionRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn($permissionObjects);
        $this->permissionsConverterMock->expects($this->exactly($callsCount))
            ->method('convertPermissionsToDisplay')
            ->with($permissionObjects)
            ->willReturn($this->permissionsData);

        $this->permissionManager->prepareAndLoadPermissions($this->productId);
    }

    /**
     * Test deleteOldPermissions method
     */
    public function testDeleteOldPermissions()
    {
        $this->productPermissionRepositoryMock->expects($this->once())
            ->method('deleteForProducts')
            ->with([$this->productId]);

        $this->permissionManager->deleteOldPermissions([$this->productId]);
    }

    /**
     * @return array
     */
    public function prepareAndLoadPermissionsProvider()
    {
        $permissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);

        return [
            [$permissionObjects],
            [[]]
        ];
    }
}
