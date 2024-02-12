<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Converter;
use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ConverterTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CmsPagePermission
 */
class ConverterTest extends TestCase
{
    /**
     * @var CmsPagePermissionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsPagePermissionInterfaceFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var array
     */
    private $permissionsDataMerged = [
        [
            Converter::STORE_IDS => [1, 2],
            Converter::CUSTOMER_GROUP_IDS => [1, 2],
            CmsPagePermissionInterface::CMS_PAGE_ID => 3,
            CmsPagePermissionInterface::VIEW_MODE => 1
        ]
    ];

    /**
     * @var array
     */
    private $permissionsDataUnmerged = [
        [
            CmsPagePermissionInterface::STORE_ID => 1,
            CmsPagePermissionInterface::CUSTOMER_GROUP_ID => 1,
            CmsPagePermissionInterface::CMS_PAGE_ID => 3,
            CmsPagePermissionInterface::VIEW_MODE => 1
        ],
        [
            CmsPagePermissionInterface::STORE_ID => 1,
            CmsPagePermissionInterface::CUSTOMER_GROUP_ID => 2,
            CmsPagePermissionInterface::CMS_PAGE_ID => 3,
            CmsPagePermissionInterface::VIEW_MODE => 1,
        ],
        [
            CmsPagePermissionInterface::STORE_ID => 2,
            CmsPagePermissionInterface::CUSTOMER_GROUP_ID => 1,
            CmsPagePermissionInterface::CMS_PAGE_ID => 3,
            CmsPagePermissionInterface::VIEW_MODE => 1
        ],
        [
            CmsPagePermissionInterface::STORE_ID => 2,
            CmsPagePermissionInterface::CUSTOMER_GROUP_ID => 2,
            CmsPagePermissionInterface::CMS_PAGE_ID => 3,
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

        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->cmsPagePermissionInterfaceFactoryMock =
            $this->createMock(CmsPagePermissionInterfaceFactory::class);

        $this->converter = $objectManager->getObject(
            Converter::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'cmsPagePermissionInterfaceFactory' => $this->cmsPagePermissionInterfaceFactoryMock,
            ]
        );
    }

    /**
     * Test convertPermissionsToSave method
     */
    public function testConvertPermissionsToSave()
    {
        $cmsPagePermissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $callsCount = count($this->permissionsDataUnmerged);
        $cmsPagePermissions = array_fill(0, $callsCount, $cmsPagePermissionMock);
        $cmsPageId = 3;

        $this->cmsPagePermissionInterfaceFactoryMock->expects($this->exactly($callsCount))
            ->method('create')
            ->willReturn($cmsPagePermissionMock);
        $this->dataObjectHelperMock->expects($this->exactly($callsCount))
            ->method('populateWithArray')
            ->withConsecutive(
                [$cmsPagePermissionMock, $this->permissionsDataUnmerged[0], CmsPagePermissionInterface::class],
                [$cmsPagePermissionMock, $this->permissionsDataUnmerged[1], CmsPagePermissionInterface::class],
                [$cmsPagePermissionMock, $this->permissionsDataUnmerged[2], CmsPagePermissionInterface::class],
                [$cmsPagePermissionMock, $this->permissionsDataUnmerged[3], CmsPagePermissionInterface::class]
            )
            ->willReturn($cmsPagePermissionMock);

        $this->assertEquals(
            $cmsPagePermissions,
            $this->converter->convertPermissionsToSave($this->permissionsDataMerged, $cmsPageId)
        );
    }

    /**
     * Test convertPermissionsToDisplay method
     */
    public function testConvertPermissionsToDisplay()
    {
        $cmsPagePermissionMock = $this->createMock(CmsPagePermission::class);
        $callsCount = count($this->permissionsDataUnmerged);
        $cmsPagePermissions = array_fill(0, $callsCount, $cmsPagePermissionMock);

        $cmsPagePermissionMock->expects($this->exactly($callsCount))
            ->method('toArray')
            ->willReturnOnConsecutiveCalls(
                $this->permissionsDataUnmerged[0],
                $this->permissionsDataUnmerged[1],
                $this->permissionsDataUnmerged[2],
                $this->permissionsDataUnmerged[3]
            );
        $this->assertEquals(
            $this->permissionsDataMerged[0],
            $this->converter->convertPermissionsToDisplay($cmsPagePermissions)
        );
    }
}
