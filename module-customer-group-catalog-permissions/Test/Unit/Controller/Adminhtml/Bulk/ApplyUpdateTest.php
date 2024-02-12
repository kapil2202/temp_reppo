<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Controller\Adminhtml\Bulk;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk\ApplyUpdate;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\Result\Json;
use Aheadworks\CustGroupCatPermissions\Api\PermissionManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\Grid\CollectionFactory as CmsPageGridCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\Grid\Collection as CmsPageGridCollection;
use Magento\Framework\App\RequestInterface;
use Aheadworks\CustGroupCatPermissions\Model\Source\Wizard\EntityType;

/**
 * Class ApplyUpdateTest
 * Test for \Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk\ApplyUpdate
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Controller\Adminhtml\Bulk
 */
class ApplyUpdateTest extends TestCase
{
    /**
     * @var ApplyUpdate
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactoryMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var ProductCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productCollectionFactoryMock;

    /**
     * @var CmsPageGridCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPageGridCollectionFactoryMock;

    /**
     * @var PermissionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionManager;

    /**
     * @var PermissionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productPermissionManager;

    /**
     * @var PermissionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPagePermissionManager;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultFactory' => $this->resultFactoryMock,
                'request' => $this->requestMock
            ]
        );
        $this->filterMock = $this->createMock(Filter::class);

        $this->productCollectionFactoryMock = $this->createPartialMock(
            ProductCollectionFactory::class,
            [
                'create'
            ]
        );
        $this->cmsPageGridCollectionFactoryMock = $this->createPartialMock(
            CmsPageGridCollectionFactory::class,
            [
                'create'
            ]
        );

        $this->categoryPermissionManager = $this->getMockForAbstractClass(
            PermissionManagerInterface::class
        );
        $this->productPermissionManager = $this->getMockForAbstractClass(
            PermissionManagerInterface::class
        );
        $this->cmsPagePermissionManager = $this->getMockForAbstractClass(
            PermissionManagerInterface::class
        );

        $this->controller = $objectManager->getObject(
            ApplyUpdate::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'collectionFactories' => [
                    EntityType::PRODUCT => $this->productCollectionFactoryMock,
                    EntityType::CMS_PAGE => $this->cmsPageGridCollectionFactoryMock
                ],
                'permissionManagers' => [
                    EntityType::CATEGORY => $this->categoryPermissionManager,
                    EntityType::PRODUCT => $this->productPermissionManager,
                    EntityType::CMS_PAGE => $this->cmsPagePermissionManager
                ]
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecuteEmptyRequestData()
    {
        $data = [];
        $result = [];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecuteValidationError()
    {
        $data = [
            ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY => '',
            ApplyUpdate::ENTITY_IDS_REQUEST_PARAM_KEY => '',
        ];
        $result = [
            'error' =>'Some of required data is missing.'
        ];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecuteCategoryMissingPermissionManager()
    {
        $firstCategoryId = 3;
        $secondCategoryId = 7;
        $entityIds = [$firstCategoryId, $secondCategoryId];
        $permissionsDataKey = 'aw_cgcp_category_permissions';
        $permissionsData = [
            [
                'record_id' => 0,
                'store_ids-prepared-for-send' => [
                    1
                ],
                'store_ids' => [
                    1
                ],
                'customer_group_ids-prepared-for-send' => [
                    0
                ],
                'customer_group_ids' => [
                    0
                ],
                'view_mode' => 1,
                'price_mode' => 2,
                'checkout_mode' => 2,
                'hidden_price_message' => 'AW test 1',
                'hidden_add_to_cart_message' => '',
            ],
        ];
        $data = [
            'isAjax' => true,
            'entityType' => 'category_123',
            'totalIds' => count($entityIds),
            'entityIds' => $entityIds,
            'permissions_changed' => 1,
            $permissionsDataKey => $permissionsData,
            'form_key' => 'test_form_key_value',
        ];
        $result = [
            'error' =>'Invalid entity type "category_123"'
        ];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY, false)
            ->willReturn(false);

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecuteCategoryMissingPermissionsData()
    {
        $firstCategoryId = 3;
        $secondCategoryId = 7;
        $entityIds = [$firstCategoryId, $secondCategoryId];
        $permissionsDataKey = 'aw_cgcp_category_permissions';
        $permissionsData = [
            [
                'record_id' => 0,
                'store_ids-prepared-for-send' => [
                    1
                ],
                'store_ids' => [
                    1
                ],
                'customer_group_ids-prepared-for-send' => [
                    0
                ],
                'customer_group_ids' => [
                    0
                ],
                'view_mode' => 1,
                'price_mode' => 2,
                'checkout_mode' => 2,
                'hidden_price_message' => 'AW test 1',
                'hidden_add_to_cart_message' => '',
            ],
        ];
        $data = [
            'isAjax' => true,
            'entityType' => EntityType::CATEGORY,
            'totalIds' => count($entityIds),
            'entityIds' => $entityIds,
            'permissions_changed' => 1,
            'aw_cgcp_category_permissions_123' => $permissionsData,
            'form_key' => 'test_form_key_value',
        ];
        $result = [
            'error' =>'Some of required data is missing.'
        ];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY, false)
            ->willReturn(false);

        $this->categoryPermissionManager->expects($this->once())
            ->method('getPermissionsDataKey')
            ->willReturn($permissionsDataKey);

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecuteCategoryErrorOnSaving()
    {
        $firstCategoryId = 3;
        $secondCategoryId = 7;
        $entityIds = [$firstCategoryId, $secondCategoryId];
        $permissionsDataKey = 'aw_cgcp_category_permissions';
        $permissionsData = [
            [
                'record_id' => 0,
                'store_ids-prepared-for-send' => [
                    1
                ],
                'store_ids' => [
                    1
                ],
                'customer_group_ids-prepared-for-send' => [
                    0
                ],
                'customer_group_ids' => [
                    0
                ],
                'view_mode' => 1,
                'price_mode' => 2,
                'checkout_mode' => 2,
                'hidden_price_message' => 'AW test 1',
                'hidden_add_to_cart_message' => '',
            ],
        ];
        $data = [
            'isAjax' => true,
            'entityType' => EntityType::CATEGORY,
            'totalIds' => count($entityIds),
            'entityIds' => $entityIds,
            'permissions_changed' => 1,
            $permissionsDataKey => $permissionsData,
            'form_key' => 'test_form_key_value',
        ];
        $errorMessage = 'Test error message';
        $result = [
            'error' => $errorMessage
        ];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY, false)
            ->willReturn(false);

        $this->categoryPermissionManager->expects($this->once())
            ->method('getPermissionsDataKey')
            ->willReturn($permissionsDataKey);
        $this->categoryPermissionManager->expects($this->once())
            ->method('prepareAndSavePermissions')
            ->with($permissionsData, $firstCategoryId)
            ->willThrowException(new \Exception($errorMessage));

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecuteCategory()
    {
        $firstCategoryId = 3;
        $secondCategoryId = 7;
        $entityIds = [$firstCategoryId, $secondCategoryId];
        $permissionsDataKey = 'aw_cgcp_category_permissions';
        $permissionsData = [
            [
                'record_id' => 0,
                'store_ids-prepared-for-send' => [
                    1
                ],
                'store_ids' => [
                    1
                ],
                'customer_group_ids-prepared-for-send' => [
                    0
                ],
                'customer_group_ids' => [
                    0
                ],
                'view_mode' => 1,
                'price_mode' => 2,
                'checkout_mode' => 2,
                'hidden_price_message' => 'AW test 1',
                'hidden_add_to_cart_message' => '',
            ],
        ];
        $data = [
            'isAjax' => true,
            'entityType' => EntityType::CATEGORY,
            'totalIds' => count($entityIds),
            'entityIds' => $entityIds,
            'permissions_changed' => 1,
            $permissionsDataKey => $permissionsData,
            'form_key' => 'test_form_key_value',
        ];
        $result = [
            'message' => __('Permissions have been applied successfully.')
        ];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY, false)
            ->willReturn(false);

        $this->categoryPermissionManager->expects($this->once())
            ->method('getPermissionsDataKey')
            ->willReturn($permissionsDataKey);
        $this->categoryPermissionManager->expects($this->exactly(count($entityIds)))
            ->method('prepareAndSavePermissions')
            ->willReturnSelf();

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecuteCmsPageGrid()
    {
        $isGrid = true;
        $firstCmsPageId = 3;
        $secondCmsPageId = 7;
        $entityIds = [$firstCmsPageId, $secondCmsPageId];
        $permissionsDataKey = 'aw_cgcp_cms_page_permissions';
        $permissionsData = [
            [
                'record_id' => 0,
                'store_ids-prepared-for-send' => [
                    2
                ],
                'store_ids' => [
                    2
                ],
                'customer_group_ids-prepared-for-send' => [
                    32000
                ],
                'customer_group_ids' => [
                    32000
                ],
                'view_mode' => 2
            ],
        ];
        $data = [
            'isAjax' => true,
            'entityType' => EntityType::CMS_PAGE,
            'totalIds' => count($entityIds),
            ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY => $isGrid,
            'selected' => $entityIds,
            'filters' => [
                'placeholder' => true
            ],
            'namespace' => 'cms_page_listing',
            'permissions_changed' => 1,
            $permissionsDataKey => $permissionsData,
            'form_key' => 'test_form_key_value',
        ];
        $result = [
            'message' => __('Permissions have been applied successfully.')
        ];

        $resultJsonMock = $this->createMock(Json::class);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($data);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(ApplyUpdate::IS_GRID_IDS_REQUEST_PARAM_KEY, false)
            ->willReturn($isGrid);

        $collectionMock = $this->createMock(CmsPageGridCollection::class);
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($entityIds);
        $this->cmsPageGridCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $this->cmsPagePermissionManager->expects($this->once())
            ->method('getPermissionsDataKey')
            ->willReturn($permissionsDataKey);
        $this->cmsPagePermissionManager->expects($this->exactly(count($entityIds)))
            ->method('prepareAndSavePermissions')
            ->willReturnSelf();

        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }
}
