<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CategoryPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Converter;
use Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ConverterTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CategoryPermission
 */
class ConverterTest extends TestCase
{
    /**
     * @var CategoryPermissionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryPermissionInterfaceFactoryMock;

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
            Converter::STORE_IDS => [1],
            Converter::CUSTOMER_GROUP_IDS => [1, 2],
            CategoryPermissionInterface::RECORD_ID => 1,
            CategoryPermissionInterface::CATEGORY_ID => 3,
            CategoryPermissionInterface::VIEW_MODE => 1,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            Converter::STORE_IDS => [2],
            Converter::CUSTOMER_GROUP_IDS => [1, 2],
            CategoryPermissionInterface::RECORD_ID => 2,
            CategoryPermissionInterface::CATEGORY_ID => 3,
            CategoryPermissionInterface::VIEW_MODE => 2,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ]
    ];

    /**
     * @var array
     */
    private $permissionsDataUnmerged = [
        [
            CategoryPermissionInterface::STORE_ID => 1,
            CategoryPermissionInterface::CUSTOMER_GROUP_ID => 1,
            CategoryPermissionInterface::RECORD_ID => 1,
            CategoryPermissionInterface::CATEGORY_ID => 3,
            CategoryPermissionInterface::VIEW_MODE => 1,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            CategoryPermissionInterface::STORE_ID => 1,
            CategoryPermissionInterface::CUSTOMER_GROUP_ID => 2,
            CategoryPermissionInterface::RECORD_ID => 1,
            CategoryPermissionInterface::CATEGORY_ID => 3,
            CategoryPermissionInterface::VIEW_MODE => 1,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            CategoryPermissionInterface::STORE_ID => 2,
            CategoryPermissionInterface::CUSTOMER_GROUP_ID => 1,
            CategoryPermissionInterface::RECORD_ID => 2,
            CategoryPermissionInterface::CATEGORY_ID => 3,
            CategoryPermissionInterface::VIEW_MODE => 2,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            CategoryPermissionInterface::STORE_ID => 2,
            CategoryPermissionInterface::CUSTOMER_GROUP_ID => 2,
            CategoryPermissionInterface::RECORD_ID => 2,
            CategoryPermissionInterface::CATEGORY_ID => 3,
            CategoryPermissionInterface::VIEW_MODE => 2,
            CategoryPermissionInterface::PRICE_MODE => 2,
            CategoryPermissionInterface::CHECKOUT_MODE => 2,
            CategoryPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            CategoryPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
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
        $this->categoryPermissionInterfaceFactoryMock =
            $this->createMock(CategoryPermissionInterfaceFactory::class);

        $this->converter = $objectManager->getObject(
            Converter::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'categoryPermissionInterfaceFactory' => $this->categoryPermissionInterfaceFactoryMock,
            ]
        );
    }

    /**
     * Test convertPermissionsToSave method
     */
    public function testConvertPermissionsToSave()
    {
        $categoryPermissionMock = $this->getMockForAbstractClass(CategoryPermissionInterface::class);
        $callsCount = count($this->permissionsDataUnmerged);
        $categoryPermissions = array_fill(0, $callsCount, $categoryPermissionMock);
        $categoryId = 3;

        $this->categoryPermissionInterfaceFactoryMock->expects($this->exactly($callsCount))
            ->method('create')
            ->willReturn($categoryPermissionMock);
        $this->dataObjectHelperMock->expects($this->exactly($callsCount))
            ->method('populateWithArray')
            ->withConsecutive(
                [$categoryPermissionMock, $this->permissionsDataUnmerged[0], CategoryPermissionInterface::class],
                [$categoryPermissionMock, $this->permissionsDataUnmerged[1], CategoryPermissionInterface::class],
                [$categoryPermissionMock, $this->permissionsDataUnmerged[2], CategoryPermissionInterface::class],
                [$categoryPermissionMock, $this->permissionsDataUnmerged[3], CategoryPermissionInterface::class]
            )
            ->willReturn($categoryPermissionMock);

        $this->assertEquals(
            $categoryPermissions,
            $this->converter->convertPermissionsToSave($this->permissionsDataMerged, $categoryId)
        );
    }

    /**
     * Test convertPermissionsToDisplay method
     */
    public function testConvertPermissionsToDisplay()
    {
        $categoryPermissionMock = $this->createMock(CategoryPermission::class);
        $callsCount = count($this->permissionsDataUnmerged);
        $categoryPermissions = array_fill(0, $callsCount, $categoryPermissionMock);

        $categoryPermissionMock->expects($this->exactly($callsCount))
            ->method('toArray')
            ->willReturnOnConsecutiveCalls(
                $this->permissionsDataUnmerged[0],
                $this->permissionsDataUnmerged[1],
                $this->permissionsDataUnmerged[2],
                $this->permissionsDataUnmerged[3]
            );
        $this->assertEquals(
            $this->permissionsDataMerged,
            $this->converter->convertPermissionsToDisplay($categoryPermissions)
        );
    }
}
