<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model;

use Aheadworks\CustGroupCatPermissions\Api\CmsPagePermissionRepositoryInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Converter as PermissionsConverter;
use Aheadworks\CustGroupCatPermissions\Model\PostPermissionDataProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermissionManager;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermissionRepository;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterfaceFactory;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterface;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CmsPagePermissionManagerTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model
 */
class CmsPagePermissionManagerTest extends TestCase
{
    /**
     * @var CmsPagePermissionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPagePermissionRepositoryMock;

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
     * @var CmsPagePermissionManager
     */
    private $permissionManager;

    /**
     * @var int
     */
    private $cmsPageId = 1;

    /**
     * @var int
     */
    private $permissionObjectsCount = 1;

    /**
     * @var array
     */
    private $permissionsData = [
        [
            PermissionsConverter::STORE_IDS => [1],
            PermissionsConverter::CUSTOMER_GROUP_IDS => [1, 2],
            CmsPagePermissionInterface::CMS_PAGE_ID => 1,
            CmsPagePermissionInterface::VIEW_MODE => 1
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

        $this->cmsPagePermissionRepositoryMock = $this->createPartialMock(
            CmsPagePermissionRepository::class,
            [
                'save',
                'getList',
                'deleteForPages'
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
            CmsPagePermissionManager::class,
            [
                'cmsPagePermissionRepository' => $this->cmsPagePermissionRepositoryMock,
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
        $permissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);

        $this->postPermissionDataProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($this->permissionsData)
            ->willReturn($this->permissionsData);
        $this->permissionsConverterMock->expects($this->once())
            ->method('convertPermissionsToSave')
            ->with($this->permissionsData, $this->cmsPageId)
            ->willReturn($permissionObjects);
        $this->cmsPagePermissionRepositoryMock->expects($this->once())
            ->method('deleteForPages')
            ->with([$this->cmsPageId]);
        $this->cmsPagePermissionRepositoryMock->expects($this->exactly($this->permissionObjectsCount))
            ->method('save')
            ->with($permissionMock)
            ->willReturn($permissionMock);

        $this->permissionManager->prepareAndSavePermissions($this->permissionsData[0], $this->cmsPageId);
    }

    /**
     * Test prepareAndSavePermissions method with exception
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Something went wrong while saving permissions.
     */
    public function testPrepareAndSavePermissionsWithException()
    {
        $permissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);
        $exception = new CouldNotSaveException(__('Test message!'));

        $this->postPermissionDataProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($this->permissionsData)
            ->willReturn($this->permissionsData);
        $this->permissionsConverterMock->expects($this->once())
            ->method('convertPermissionsToSave')
            ->with($this->permissionsData, $this->cmsPageId)
            ->willReturn($permissionObjects);
        $this->cmsPagePermissionRepositoryMock->expects($this->once())
            ->method('deleteForPages')
            ->with([$this->cmsPageId]);
        $this->cmsPagePermissionRepositoryMock->expects($this->once())
            ->method('save')
            ->with($permissionMock)
            ->willThrowException($exception);
        $this->expectException(\Exception::class);
        $this->permissionManager->prepareAndSavePermissions($this->permissionsData[0], $this->cmsPageId);
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
        $searchResultsMock = $this->getMockForAbstractClass(CmsPagePermissionSearchResultsInterface::class);
        $callsCount = empty($permissionObjects) ? 0 : 1;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(CmsPagePermissionInterface::CMS_PAGE_ID, $this->cmsPageId)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->cmsPagePermissionRepositoryMock->expects($this->once())
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

        $this->permissionManager->prepareAndLoadPermissions($this->cmsPageId);
    }

    /**
     * Test deleteOldPermissions method
     */
    public function testDeleteOldPermissions()
    {
        $this->cmsPagePermissionRepositoryMock->expects($this->once())
            ->method('deleteForPages')
            ->with([$this->cmsPageId]);

        $this->permissionManager->deleteOldPermissions([$this->cmsPageId]);
    }

    /**
     * @return array
     */
    public function prepareAndLoadPermissionsProvider()
    {
        $permissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $permissionObjects = array_fill(0, $this->permissionObjectsCount, $permissionMock);

        return [
            [$permissionObjects],
            [[]]
        ];
    }
}
