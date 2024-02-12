<?php
namespace Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CmsPagePermission;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Applier;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Resolver;
use Aheadworks\CustGroupCatPermissions\Model\Source\AccessMode;
use Aheadworks\CustGroupCatPermissions\Model\ResourceModel\CmsPagePermission as PermissionResource;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ResolverTest
 * @package Aheadworks\CustGroupCatPermissions\Test\Unit\Model\CmsPagePermission
 */
class ApplierTest extends TestCase
{
    /**
     * @var Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionResolverMock;

    /**
     * @var PermissionResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->permissionResolverMock = $this->createMock(Resolver::class);
        $this->resourceMock = $this->createMock(PermissionResource::class);
        $this->applier = $objectManager->getObject(
            Applier::class,
            [
                'resolver' => $this->permissionResolverMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Test applyForCmsPage method
     *
     * @param bool $isApplied
     * @param int $viewMode
     * @dataProvider applyForCmsPageProvider
     */
    public function testApplyForCmsPage($isApplied, $viewMode)
    {
        $cmsPageMock = $this->createMock(CmsPage::class);
        $permissionMock = $this->getMockForAbstractClass(CmsPagePermissionInterface::class);
        $callsCount = $isApplied ? 0 : 1;

        $cmsPageMock->expects($this->atLeastOnce())
            ->method('getData')
            ->with(Applier::PERMISSION_APPLIED)
            ->willReturn($isApplied);
        $cmsPageMock->expects($this->atLeast($callsCount))
            ->method('setData')
            ->willReturnSelf();
        $this->permissionResolverMock->expects($this->exactly($callsCount))
            ->method('getPermissionForCmsPage')
            ->with($cmsPageMock)
            ->willReturn($permissionMock);
        $permissionMock->expects($this->atLeast($callsCount))
            ->method('getViewMode')
            ->willReturn($viewMode);

        $this->applier->applyForCmsPage($cmsPageMock);
    }

    /**
     * @return array
     */
    public function applyForCmsPageProvider()
    {
        return [
            [false, AccessMode::SHOW],
            [true, AccessMode::HIDE]
        ];
    }
}
