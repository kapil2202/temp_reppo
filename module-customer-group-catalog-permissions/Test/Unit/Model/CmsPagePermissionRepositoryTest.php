<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model;

use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermissionRepository;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission as CmsPagePermissionModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission as CmsPagePermissionResourceModel;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission\Collection
    as CmsPagePermissionCollection;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission\CollectionFactory
    as CmsPagePermissionCollectionFactory;
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
 * Class CmsPagePermissionRepositoryTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model
 */
class CmsPagePermissionRepositoryTest extends TestCase
{
    /**
     * @var CmsPagePermissionResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var CmsPagePermissionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPagePermissionInterfaceFactoryMock;

    /**
     * @var CmsPagePermissionCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPagePermissionCollectionFactoryMock;

    /**
     * @var CmsPagePermissionSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var CmsPagePermissionRepository
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
            CmsPagePermissionResourceModel::class,
            [
                'load',
                'delete',
                'save',
                'deleteForPages',
                'getPermissionIdByCmsPageId'
            ]
        );
        $this->cmsPagePermissionInterfaceFactoryMock =
            $this->createMock(CmsPagePermissionInterfaceFactory::class);
        $this->cmsPagePermissionCollectionFactoryMock =
            $this->createMock(CmsPagePermissionCollectionFactory::class);
        $this->searchResultsFactoryMock =
            $this->createMock(CmsPagePermissionSearchResultsInterfaceFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->repository = $objectManager->getObject(
            CmsPagePermissionRepository::class,
            [
                'resource' => $this->resourceMock,
                'cmsPagePermissionInterfaceFactory' => $this->cmsPagePermissionInterfaceFactoryMock,
                'cmsPagePermissionCollectionFactory' => $this->cmsPagePermissionCollectionFactoryMock,
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
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);

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
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);
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
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);

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
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);
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
     * Test deleteForPages method
     */
    public function testDeleteForPages()
    {
        $cmsPageIds = [1, 2, 3];

        $this->resourceMock->expects($this->once())
            ->method('deleteForPages')
            ->with($cmsPageIds);

        $this->repository->deleteForPages($cmsPageIds);
    }

    /**
     * Test getById method
     */
    public function testGetById()
    {
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);

        $this->cmsPagePermissionInterfaceFactoryMock->expects($this->once())
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
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);

        $this->cmsPagePermissionInterfaceFactoryMock->expects($this->once())
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
     * Test getForCmsPage method
     *
     * @param int $storeId
     * @dataProvider getForCmsPageProvider
     */
    public function testGetForCmsPage($storeId)
    {
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $cmsPageId = 3;
        $customerGroupId = 1;
        $callCount = $storeId ? 0 : 1;

        $this->storeManagerMock->expects($this->exactly($callCount))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly($callCount))
            ->method('getId')
            ->willReturn($storeId);
        $this->cmsPagePermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('getPermissionIdByCmsPageId')
            ->with($cmsPageId, $customerGroupId, $storeId)
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
            $this->repository->getForCmsPage($cmsPageId, $customerGroupId, $storeId)
        );
    }

    /**
     * Test getForCmsPage method with exception
     *
     * @param int $storeId
     * @dataProvider getForCmsPageProvider
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with permission_id = 1
     */
    public function testGetForCmsPageWithException($storeId)
    {
        $permissionMock = $this->createMock(CmsPagePermissionModel::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $cmsPageId = 3;
        $customerGroupId = 1;
        $callCount = $storeId ? 0 : 1;

        $this->storeManagerMock->expects($this->exactly($callCount))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly($callCount))
            ->method('getId')
            ->willReturn($storeId);
        $this->cmsPagePermissionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($permissionMock);
        $this->resourceMock->expects($this->once())
            ->method('getPermissionIdByCmsPageId')
            ->with($cmsPageId, $customerGroupId, $storeId)
            ->willReturn($this->permissionId);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($permissionMock, $this->permissionId)
            ->willReturnSelf();
        $permissionMock->expects($this->once())
            ->method('getPermissionId')
            ->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->repository->getForCmsPage($cmsPageId, $customerGroupId, $storeId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $cmsPagePermissionModelMock = $this->createMock(CmsPagePermissionModel::class);
        $cmsPagePermissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(CmsPagePermissionSearchResultsInterface::class);
        $cmsPagePermissionCollectionMock = $this->createPartialMock(
            CmsPagePermissionCollection::class,
            [
                'getSize',
                'getItems'
            ]
        );
        $searchResultItems = [$cmsPagePermissionMock];
        $collectionItems = [$cmsPagePermissionModelMock];
        $collectionSize = count($collectionItems);
        $cmsPagePermissionData = [
            CmsPagePermissionInterface::ID => $this->permissionId,
            CmsPagePermissionInterface::STORE_ID => 1,
            CmsPagePermissionInterface::CUSTOMER_GROUP_ID => 1,
            CmsPagePermissionInterface::VIEW_MODE => 1
        ];

        $cmsPagePermissionModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($cmsPagePermissionData);
        $cmsPagePermissionCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $cmsPagePermissionCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);
        $this->cmsPagePermissionCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cmsPagePermissionCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($cmsPagePermissionCollectionMock, CmsPagePermissionInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $cmsPagePermissionCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);
        $this->cmsPagePermissionInterfaceFactoryMock->expects($this->exactly($collectionSize))
            ->method('create')
            ->willReturn($cmsPagePermissionMock);
        $this->dataObjectHelperMock->expects($this->exactly($collectionSize))
            ->method('populateWithArray')
            ->with($cmsPagePermissionMock, $cmsPagePermissionData, CmsPagePermissionInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($searchResultItems)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->repository->getList($searchCriteriaMock));
    }

    /**
     * @return array
     */
    public function getForCmsPageProvider()
    {
        return [
            [1],
            [null]
        ];
    }
}
