<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver\CurrentCategory as CurrentCategoryResolver;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Aheadworks\CustGroupCatPermissions\Model\Service\ResponseManager;

/**
 * Class CurrentCategoryTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\ProductPermission\Resolver
 */
class CurrentCategoryTest extends TestCase
{
    /**
     * @var CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepositoryMock;

    /**
     * @var CategoryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryCollectionFactoryMock;

    /**
     * @var CurrentCategoryResolver
     */
    private $resolver;

    /**
     * @var ResponseManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $responseManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->categoryRepositoryMock = $this->getMockForAbstractClass(CategoryRepositoryInterface::class);
        $this->responseManagerMock = $this->createMock(ResponseManager::class);
        $this->categoryCollectionFactoryMock = $this->createMock(CategoryCollectionFactory::class);
        $this->resolver = $objectManager->getObject(
            CurrentCategoryResolver::class,
            [
                'categoryRepository' => $this->categoryRepositoryMock,
                'responseManager' => $this->responseManagerMock,
                'categoryCollectionFactory' => $this->categoryCollectionFactoryMock,
            ]
        );
    }

    /**
     * Test getCurrentCategory method
     */
    public function testGetCurrentCategory()
    {
        $productMock = $this->createMock(Product::class);
        $categoryMock = $this->createMock(Category::class);
        $productCategoryId = 4;
        $productMock->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($productCategoryId);

        $this->categoryRepositoryMock->expects($this->any())
            ->method('get')
            ->with($productCategoryId)
            ->willReturn($categoryMock);

        $this->assertSame($categoryMock, $this->resolver->getCurrentCategory($productMock));
    }

    /**
     * Test getCurrentCategory method with exception
     */
    public function testGetCurrentCategoryWithException()
    {
        $productMock = $this->createMock(Product::class);
        $collectionMock = $this->createMock(\Magento\Framework\Data\Collection::class);
        $categoryMock = $this->createMock(\Magento\Catalog\Model\Category::class);
        $exception = new NoSuchEntityException(__('No such entity!'));
        $categoryId = null;

        $productMock->expects($this->once())
            ->method('getCategoryId')
            ->willReturn(null);
        $this->categoryRepositoryMock->expects($this->any())
            ->method('get')
            ->with($categoryId)
            ->willThrowException($exception);
        $productMock->expects($this->once())
            ->method('getCategoryCollection')
            ->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$categoryMock]);

        $this->assertSame([$categoryMock], $this->resolver->getCurrentCategory($productMock));
    }
}
