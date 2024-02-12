<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model;

use Aheadworks\CustGroupCatPermissions\Model\CategoryPermissionRepository;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission as CategoryPermissionModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission as CategoryPermissionResourceModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission\Collection
    as CategoryPermissionCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CategoryPermission\CollectionFactory
    as CategoryPermissionCollectionFactory;
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
 * Class CategoryPermissionRepositoryTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model
 */
class CategoryPermissionRepositoryTest extends TestCase
{
    /**
     * @var CategoryPermissionResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var CategoryPermissionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionInterfaceFactoryMock;

    /**
     * @var CategoryPermissionCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionCollectionFactoryMock;

    /**
     * @var CategoryPermissionSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var CategoryPermissionRepository
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
            CategoryPermissionResourceModel::class,
            [
                'load',
                'delete',
                'save',
                'deleteForCategories',
                'getPermissionIdForCategory'
            ]
        );
        $this->categoryPermissionInterfaceFactoryMock =
            $this->createMock(CategoryPermissionInterfaceFactory::class);
        $this->categoryPermissionCollectionFactoryMock =
            $this->createMock(CategoryPermissionCollectionFactory::class);
        $this->searchResultsFactoryMock =
            $this->createMock(CategoryPermissionSearchResultsInterfaceFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->repository = $objectManager->getObject(
            CategoryPermissionRepository::class,
            [
                'resource' => $this->resourceMock,
                'categoryPermissionInterfaceFactory' => $this->categoryPermissionInterfaceFactoryMock,
                'categoryPermissionCollectionFactory' => $this->categoryPermissionCollectionFactoryMock,
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
        $permissionMock = $this->createMock(CategoryPermissionModel::class);

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
        $permissionMock = $this->createMock(CategoryPermissionModel::class);
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
        $permissionMock = $this->createMock(CategoryPermissionModel::class);

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
        $permissionMock = $this->createMock(CategoryPermissionModel::class);
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
     * Test deleteForCategories method
     */
    public function testDeleteForCategories()
    {
        $categoryIds = [1, 2, 3];

        $this->resourceMock->expects($this->once())
            ->method('deleteForCategories')
            ->with($categoryIds);

        $this->repository->deleteForCategories($categoryIds);
    }

    /**
     * Test getById method
     */
    public function testGetById()
    {
        $permissionMock = $this->createMock(CategoryPermissionModel::class);

        $this->categoryPermissionInterfaceFactoryMock->expects($this->once())
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
        $permissionMock = $this->createMock(CategoryPermissionModel::class);

        $this->categoryPermissionInterfaceFactoryMock->expects($this->once())
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
     * Test getForCategory method
     *
     * @param int $storeId
     * @dataProvider getForCategoryProvider
     */
    public function testGetForCategory($storeId)
    {
        $permissionMock = $this->createMock(CategoryPermissionModel::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $categoryId = 3;
        $parentIds = [1, 2];
        $customerGroupId = 1;
        $callCount = $storeId ? 0 : 1;

        $this->storeManagerMock->expects($this->exactly($callCount))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly($callCount))
            ->method('getId')
            ->willReturn($storeId);
        $this->categoryPermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('getPermissionIdForCategory')
            ->with($categoryId, $parentIds, $customerGroupId, $storeId)
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
            $this->repository->getForCategory($categoryId, $parentIds, $customerGroupId, $storeId)
        );
    }

    /**
     * Test getForCategory method with exception
     *
     * @param int $storeId
     * @dataProvider getForCategoryProvider
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with permission_id = 1
     */
    public function testGetForCategoryWithException($storeId)
    {
        $permissionMock = $this->createMock(CategoryPermissionModel::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $categoryId = 3;
        $parentIds = [1, 2];
        $customerGroupId = 1;
        $callCount = $storeId ? 0 : 1;

        $this->storeManagerMock->expects($this->exactly($callCount))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly($callCount))
            ->method('getId')
            ->willReturn($storeId);
        $this->categoryPermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('getPermissionIdForCategory')
            ->with($categoryId, $parentIds, $customerGroupId, $storeId)
            ->willReturn($this->permissionId);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($permissionMock, $this->permissionId)
            ->willReturnSelf();
        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->repository->getForCategory($categoryId, $parentIds, $customerGroupId, $storeId);
    }
    
    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $categoryPermissionModelMock = $this->createMock(CategoryPermissionModel::class);
        $categoryPermissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(CategoryPermissionSearchResultsInterface::class);
        $categoryPermissionCollectionMock = $this->createPartialMock(
            CategoryPermissionCollection::class,
            [
                'getSize',
                'getItems'
            ]
        );
        $searchResultItems = [$categoryPermissionMock];
        $collectionItems = [$categoryPermissionModelMock];
        $collectionSize = count($collectionItems);
        $categoryPermissionData = [
            CategoryPermissionInterface::ID => $this->permissionId,
            CategoryPermissionInterface::RECORD_ID => 1,
            CategoryPermissionInterface::STORE_ID => 1,
            CategoryPermissionInterface::CUSTOMER_GROUP_ID => 1,
            CategoryPermissionInterface::VIEW_MODE => 1
        ];

        $categoryPermissionModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($categoryPermissionData);
        $categoryPermissionCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $categoryPermissionCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);
        $this->categoryPermissionCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($categoryPermissionCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($categoryPermissionCollectionMock, CategoryPermissionInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $categoryPermissionCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);
        $this->categoryPermissionInterfaceFactoryMock->expects($this->exactly($collectionSize))
            ->method('create')
            ->willReturn($categoryPermissionMock);
        $this->dataObjectHelperMock->expects($this->exactly($collectionSize))
            ->method('populateWithArray')
            ->with($categoryPermissionMock, $categoryPermissionData, CategoryPermissionInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($searchResultItems)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->repository->getList($searchCriteriaMock));
    }

    /**
     * @return array
     */
    public function getForCategoryProvider()
    {
        return [
            [1],
            [null]
        ];
    }
}
