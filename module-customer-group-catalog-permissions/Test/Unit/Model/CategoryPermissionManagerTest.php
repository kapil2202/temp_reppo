<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model;

use Aheadworks\CustGroupCatPermissions\Api\CategoryPermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Converter as PermissionsConverter;
use Aheadworks\CustGroupCatPermissions\Model\PostPermissionDataProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermissionManager;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermissionRepository;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CategoryPermissionManagerTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model
 */
class CategoryPermissionManagerTest extends TestCase
{
    /**
     * @var CategoryPermissionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionRepositoryMock;

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
     * @var CategoryPermissionManager
     */
    private $permissionManager;

    /**
     * @var int
     */
    private $categoryId = 3;

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
                CategoryPermissionInterface::RECORD_ID => 1,
                CategoryPermissionInterface::CATEGORY_ID => 2,
                CategoryPermissionInterface::VIEW_MODE => 1,
                CategoryPermissionInterface::PRICE_MODE => 2,
                CategoryPermissionInterface::CHECKOUT_MODE => 2
            ],
            [
                PermissionsConverter::STORE_IDS => [2],
                PermissionsConverter::CUSTOMER_GROUP_IDS => [1, 2],
                CategoryPermissionInterface::RECORD_ID => 2,
                CategoryPermissionInterface::CATEGORY_ID => 2,
                CategoryPermissionInterface::VIEW_MODE => 1,
                CategoryPermissionInterface::PRICE_MODE => 2,
                CategoryPermissionInterface::CHECKOUT_MODE => 2
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

        $this->categoryPermissionRepositoryMock = $this->createPartialMock(
            CategoryPermissionRepository::class,
            [
                'save',
                'getList',
                'deleteForCategories'
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
            CategoryPermissionManager::class,
            [
                'categoryPermissionRepository' => $this->categoryPermissionRepositoryMock,
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
        $permissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);

        $this->postPermissionDataProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($this->permissionsData)
            ->willReturn($this->permissionsData);
        $this->permissionsConverterMock->expects($this->once())
            ->method('convertPermissionsToSave')
            ->with($this->permissionsData, $this->categoryId)
            ->willReturn($permissionObjects);
        $this->categoryPermissionRepositoryMock->expects($this->once())
            ->method('deleteForCategories')
            ->with([$this->categoryId]);
        $this->categoryPermissionRepositoryMock->expects($this->exactly($this->permissionObjectsCount))
            ->method('save')
            ->with($permissionMock)
            ->willReturn($permissionMock);

        $this->permissionManager->prepareAndSavePermissions($this->permissionsData, $this->categoryId);
    }

    /**
     * Test prepareAndSavePermissions method with exception
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Something went wrong while saving permissions.
     */
    public function testPrepareAndSavePermissionsWithException()
    {
        $permissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);
        $exception = new CouldNotSaveException(__('Test message!'));
        $this->expectException(\Exception::class);
        $this->postPermissionDataProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($this->permissionsData)
            ->willReturn($this->permissionsData);
        $this->permissionsConverterMock->expects($this->once())
            ->method('convertPermissionsToSave')
            ->with($this->permissionsData, $this->categoryId)
            ->willReturn($permissionObjects);
        $this->categoryPermissionRepositoryMock->expects($this->once())
            ->method('deleteForCategories')
            ->with([$this->categoryId]);
        $this->categoryPermissionRepositoryMock->expects($this->once())
            ->method('save')
            ->with($permissionMock)
            ->willThrowException($exception);

        $this->permissionManager->prepareAndSavePermissions($this->permissionsData, $this->categoryId);
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
        $searchResultsMock = $this->getMockForAbstractClass(CategoryPermissionSearchResultsInterface::class);
        $callsCount = empty($permissionObjects) ? 0 : 1;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(CategoryPermissionInterface::CATEGORY_ID, $this->categoryId)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->categoryPermissionRepositoryMock->expects($this->once())
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

        $this->permissionManager->prepareAndLoadPermissions($this->categoryId);
    }

    /**
     * Test deleteOldPermissions method
     */
    public function testDeleteOldPermissions()
    {
        $this->categoryPermissionRepositoryMock->expects($this->once())
            ->method('deleteForCategories')
            ->with([$this->categoryId]);

        $this->permissionManager->deleteOldPermissions([$this->categoryId]);
    }

    /**
     * @return array
     */
    public function prepareAndLoadPermissionsProvider()
    {
        $permissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);

        return [
            [$permissionObjects],
            [[]]
        ];
    }
}
