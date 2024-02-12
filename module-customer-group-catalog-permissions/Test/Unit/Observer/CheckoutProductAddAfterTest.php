<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Observer;

use Aheadworks\CustGroupCatPermissions\Observer\CheckoutProductAddAfter;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class CheckoutProductAddAfterTest
 *  Test for \Aheadworks\CustGroupCatPermissions\Observer\CheckoutProductAddAfter
 *
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Observer
 */
class CheckoutProductAddAfterTest extends TestCase
{
    /**
     * @var CheckoutProductAddAfter
     */
    private $observer;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Applier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionApplierMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);

        $this->permissionApplierMock = $this->createMock(Applier::class);

        $this->observer = $objectManager->getObject(
            CheckoutProductAddAfter::class,
            [
                'config' => $this->configMock,
                'permissionApplier' => $this->permissionApplierMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecuteModuleDisabled()
    {
        $observerMock = $this->createMock(Observer::class);
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     */
    public function testExecuteNoNeedToRemoveItem()
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->atLeastOnce())
            ->method('getData')
            ->willReturnMap(
                [
                    [Applier::HIDE_PRODUCT, false],
                    [Applier::HIDE_PRICE, false],
                    [Applier::HIDE_ADD_TO_CART, false],
                ]
            );

        $quoteItemMock = $this->createMock(QuoteItem::class);

        $eventMock = $this->getMockBuilder(Event::class)
                          ->disableOriginalConstructor()
                          ->setMethods(['getQuoteItem', 'getProduct'])
                          ->getMock();
        $eventMock->expects($this->once())
            ->method('getQuoteItem')
            ->willReturn($quoteItemMock);
        $eventMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForProduct')
            ->with($productMock)
            ->willReturnSelf();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * Test execute method
     *
     * @param array $productDataMap
     * @dataProvider executeRemoveItemDataProvider
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Sorry, you are not allowed to add this product to your cart.
     */
    public function testExecuteRemoveItem($productDataMap)
    {
        $quoteItemId = 1;

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->atLeastOnce())
            ->method('getData')
            ->willReturnMap(
                $productDataMap
            );

        $quoteMock = $this->createMock(Quote::class);
        $quoteMock->expects($this->once())
            ->method('removeItem')
            ->with($quoteItemId)
            ->willReturnSelf();

        $quoteItemMock = $this->createMock(QuoteItem::class);
        $quoteItemMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $quoteItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteItemId);

        $eventMock = $this->getMockBuilder(Event::class)
                          ->disableOriginalConstructor()
                          ->setMethods(['getQuoteItem', 'getProduct'])
                          ->getMock();
        $eventMock->expects($this->once())
            ->method('getQuoteItem')
            ->willReturn($quoteItemMock);
        $eventMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->permissionApplierMock->expects($this->once())
            ->method('applyForProduct')
            ->with($productMock)
            ->willReturnSelf();
        $this->expectException(LocalizedException::class);
        $this->observer->execute($observerMock);
    }

    /**
     * Data provider for execute remove item test
     *
     * @return array
     */
    public function executeRemoveItemDataProvider()
    {
        return [
            [
                [
                    [Applier::HIDE_PRODUCT, null, true],
                    [Applier::HIDE_PRICE, null, true],
                    [Applier::HIDE_ADD_TO_CART, null, true],
                ]
            ],
            [
                [
                    [Applier::HIDE_PRODUCT, null, false],
                    [Applier::HIDE_PRICE, null, true],
                    [Applier::HIDE_ADD_TO_CART, null, true],
                ]
            ],
            [
                [
                    [Applier::HIDE_PRODUCT, null, false],
                    [Applier::HIDE_PRICE, null, false],
                    [Applier::HIDE_ADD_TO_CART, null, true],
                ]
            ],
        ];
    }
}
