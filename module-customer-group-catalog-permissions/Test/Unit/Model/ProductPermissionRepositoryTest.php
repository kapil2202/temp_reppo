<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model;

use Aheadworks\CustGroupCatPermissions\Model\ProductPermissionRepository;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionSearchResultsInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission as ProductPermissionModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission as ProductPermissionResourceModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission\Collection
    as ProductPermissionCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\ProductPermission\CollectionFactory
    as ProductPermissionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ProductPermissionRepositoryTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model
 */
class ProductPermissionRepositoryTest extends TestCase
{
    /**
     * @var ProductPermissionResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var ProductPermissionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productPermissionInterfaceFactoryMock;

    /**
     * @var ProductPermissionCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productPermissionCollectionFactoryMock;

    /**
     * @var ProductPermissionSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var ProductPermissionRepository
     */
    private $repository;

    /**
     * @var int
     */
    private $permissionId = 1;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->createPartialMock(
            ProductPermissionResourceModel::class,
            [
                'load',
                'delete',
                'save',
                'deleteForProducts',
                'getPermissionIdByProductId'
            ]
        );
        $this->productPermissionInterfaceFactoryMock =
            $this->createMock(ProductPermissionInterfaceFactory::class);
        $this->productPermissionCollectionFactoryMock =
            $this->createMock(ProductPermissionCollectionFactory::class);
        $this->searchResultsFactoryMock =
            $this->createMock(ProductPermissionSearchResultsInterfaceFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->repository = $objectManager->getObject(
            ProductPermissionRepository::class,
            [
                'resource' => $this->resourceMock,
                'productPermissionInterfaceFactory' => $this->productPermissionInterfaceFactoryMock,
                'productPermissionCollectionFactory' => $this->productPermissionCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);

        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn($this->permissionId);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($permissionMock)
            ->willReturnSelf();

        $this->assertSame($permissionMock, $this->repository->save($permissionMock));
    }

    /**
     * Test save method with exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Test message!
     */
    public function testSaveWithException()
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);
        $exception = new \Exception('Test message!');

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($permissionMock)
            ->willThrowException($exception);
        $permissionMock->expects($this->never())
            ->method('getPermissionId');
        $this->expectException(CouldNotSaveException::class);
        $this->repository->save($permissionMock);
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);

        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn($this->permissionId);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($permissionMock)
            ->willReturnSelf();

        $this->assertTrue($this->repository->delete($permissionMock));
    }

    /**
     * Test delete method with exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Test message!
     */
    public function testDeleteWithException()
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);
        $exception = new \Exception('Test message!');

        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($permissionMock)
            ->willThrowException($exception);
        $permissionMock->expects($this->never())
            ->method('getPermissionId');
        $this->expectException(CouldNotDeleteException::class);
        $this->repository->delete($permissionMock);
    }

    /**
     * Test deleteForProducts method
     */
    public function testDeleteForProducts()
    {
        $productIds = [1, 2, 3];

        $this->resourceMock->expects($this->once())
            ->method('deleteForProducts')
            ->with($productIds);

        $this->repository->deleteForProducts($productIds);
    }

    /**
     * Test getById method
     */
    public function testGetById()
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);

        $this->productPermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($permissionMock, $this->permissionId)
            ->willReturnSelf();
        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn($this->permissionId);

        $this->assertSame($permissionMock, $this->repository->getById($this->permissionId));
    }

    /**
     * Test getById method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with permission_id = 1
     */
    public function testGetByIdWithException()
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);

        $this->productPermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($permissionMock, $this->permissionId)
            ->willReturnSelf();
        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->repository->getById($this->permissionId);
    }

    /**
     * Test getForProduct method
     *
     * @param int $storeId
     * @dataProvider getForProductProvider
     */
    public function testGetForProduct($storeId)
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $productId = 3;
        $customerGroupId = 1;
        $callCount = $storeId ? 0 : 1;

        $this->storeManagerMock->expects($this->exactly($callCount))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly($callCount))
            ->method('getId')
            ->willReturn($storeId);
        $this->productPermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('getPermissionIdByProductId')
            ->with($productId, $customerGroupId, $storeId)
            ->willReturn($this->permissionId);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($permissionMock, $this->permissionId)
            ->willReturnSelf();
        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn($this->permissionId);

        $this->assertSame(
            $permissionMock,
            $this->repository->getForProduct($productId, $customerGroupId, $storeId)
        );
    }

    /**
     * Test getForProduct method with exception
     *
     * @param int $storeId
     * @dataProvider getForProductProvider
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with permission_id = 1
     */
    public function testGetForProductWithException($storeId)
    {
        $permissionMock = $this->createMock(ProductPermissionModel::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $productId = 3;
        $customerGroupId = 1;
        $callCount = $storeId ? 0 : 1;

        $this->storeManagerMock->expects($this->exactly($callCount))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly($callCount))
            ->method('getId')
            ->willReturn($storeId);
        $this->productPermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('getPermissionIdByProductId')
            ->with($productId, $customerGroupId, $storeId)
            ->willReturn($this->permissionId);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($permissionMock, $this->permissionId)
            ->willReturnSelf();
        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->repository->getForProduct($productId, $customerGroupId, $storeId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $productPermissionModelMock = $this->createMock(ProductPermissionModel::class);
        $productPermissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(ProductPermissionSearchResultsInterface::class);
        $productPermissionCollectionMock = $this->createPartialMock(
            ProductPermissionCollection::class,
            [
                'getSize',
                'getItems'
            ]
        );
        $searchResultItems = [$productPermissionMock];
        $collectionItems = [$productPermissionModelMock];
        $collectionSize = count($collectionItems);
        $productPermissionData = [
            ProductPermissionInterface::ID => $this->permissionId,
            ProductPermissionInterface::RECORD_ID => 1,
            ProductPermissionInterface::STORE_ID => 1,
            ProductPermissionInterface::CUSTOMER_GROUP_ID => 1,
            ProductPermissionInterface::VIEW_MODE => 1
        ];

        $productPermissionModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($productPermissionData);
        $productPermissionCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $productPermissionCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);
        $this->productPermissionCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productPermissionCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($productPermissionCollectionMock, ProductPermissionInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $productPermissionCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);
        $this->productPermissionInterfaceFactoryMock->expects($this->exactly($collectionSize))
            ->method('create')
            ->willReturn($productPermissionMock);
        $this->dataObjectHelperMock->expects($this->exactly($collectionSize))
            ->method('populateWithArray')
            ->with($productPermissionMock, $productPermissionData, ProductPermissionInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($searchResultItems)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->repository->getList($searchCriteriaMock));
    }

    /**
     * @return array
     */
    public function getForProductProvider()
    {
        return [
            [1],
            [null]
        ];
    }
}
