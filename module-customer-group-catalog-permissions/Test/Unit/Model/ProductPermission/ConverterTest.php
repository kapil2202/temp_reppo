<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Converter;
use Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ConverterTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission
 */
class ConverterTest extends TestCase
{
    /**
     * @var ProductPermissionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productPermissionInterfaceFactoryMock;

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
            ProductPermissionInterface::RECORD_ID => 1,
            ProductPermissionInterface::PRODUCT_ID => 3,
            ProductPermissionInterface::VIEW_MODE => 1,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            Converter::STORE_IDS => [2],
            Converter::CUSTOMER_GROUP_IDS => [1, 2],
            ProductPermissionInterface::RECORD_ID => 2,
            ProductPermissionInterface::PRODUCT_ID => 3,
            ProductPermissionInterface::VIEW_MODE => 2,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ]
    ];

    /**
     * @var array
     */
    private $permissionsDataUnmerged = [
        [
            ProductPermissionInterface::STORE_ID => 1,
            ProductPermissionInterface::CUSTOMER_GROUP_ID => 1,
            ProductPermissionInterface::RECORD_ID => 1,
            ProductPermissionInterface::PRODUCT_ID => 3,
            ProductPermissionInterface::VIEW_MODE => 1,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            ProductPermissionInterface::STORE_ID => 1,
            ProductPermissionInterface::CUSTOMER_GROUP_ID => 2,
            ProductPermissionInterface::RECORD_ID => 1,
            ProductPermissionInterface::PRODUCT_ID => 3,
            ProductPermissionInterface::VIEW_MODE => 1,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            ProductPermissionInterface::STORE_ID => 2,
            ProductPermissionInterface::CUSTOMER_GROUP_ID => 1,
            ProductPermissionInterface::RECORD_ID => 2,
            ProductPermissionInterface::PRODUCT_ID => 3,
            ProductPermissionInterface::VIEW_MODE => 2,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
        ],
        [
            ProductPermissionInterface::STORE_ID => 2,
            ProductPermissionInterface::CUSTOMER_GROUP_ID => 2,
            ProductPermissionInterface::RECORD_ID => 2,
            ProductPermissionInterface::PRODUCT_ID => 3,
            ProductPermissionInterface::VIEW_MODE => 2,
            ProductPermissionInterface::PRICE_MODE => 2,
            ProductPermissionInterface::CHECKOUT_MODE => 2,
            ProductPermissionInterface::HIDDEN_PRICE_MESSAGE => null,
            ProductPermissionInterface::HIDDEN_ADD_TO_CART_MESSAGE => null
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
        $this->productPermissionInterfaceFactoryMock =
            $this->createMock(ProductPermissionInterfaceFactory::class);

        $this->converter = $objectManager->getObject(
            Converter::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'productPermissionInterfaceFactory' => $this->productPermissionInterfaceFactoryMock,
            ]
        );
    }

    /**
     * Test convertPermissionsToSave method
     */
    public function testConvertPermissionsToSave()
    {
        $productPermissionMock = $this->getMockForAbstractClass(ProductPermissionInterface::class);
        $callsCount = count($this->permissionsDataUnmerged);
        $productPermissions = array_fill(0, $callsCount, $productPermissionMock);
        $productId = 3;

        $this->productPermissionInterfaceFactoryMock->expects($this->exactly($callsCount))
            ->method('create')
            ->willReturn($productPermissionMock);
        $this->dataObjectHelperMock->expects($this->exactly($callsCount))
            ->method('populateWithArray')
            ->withConsecutive(
                [$productPermissionMock, $this->permissionsDataUnmerged[0], ProductPermissionInterface::class],
                [$productPermissionMock, $this->permissionsDataUnmerged[1], ProductPermissionInterface::class],
                [$productPermissionMock, $this->permissionsDataUnmerged[2], ProductPermissionInterface::class],
                [$productPermissionMock, $this->permissionsDataUnmerged[3], ProductPermissionInterface::class]
            )
            ->willReturn($productPermissionMock);

        $this->assertEquals(
            $productPermissions,
            $this->converter->convertPermissionsToSave($this->permissionsDataMerged, $productId)
        );
    }

    /**
     * Test convertPermissionsToDisplay method
     */
    public function testConvertPermissionsToDisplay()
    {
        $productPermissionMock = $this->createMock(ProductPermission::class);
        $callsCount = count($this->permissionsDataUnmerged);
        $productPermissions = array_fill(0, $callsCount, $productPermissionMock);

        $productPermissionMock->expects($this->exactly($callsCount))
            ->method('toArray')
            ->willReturnOnConsecutiveCalls(
                $this->permissionsDataUnmerged[0],
                $this->permissionsDataUnmerged[1],
                $this->permissionsDataUnmerged[2],
                $this->permissionsDataUnmerged[3]
            );
        $this->assertEquals(
            $this->permissionsDataMerged,
            $this->converter->convertPermissionsToDisplay($productPermissions)
        );
    }
}
