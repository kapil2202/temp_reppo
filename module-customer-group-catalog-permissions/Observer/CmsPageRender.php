<?php
namespace Aheadworks\CustGroupCatPermissions\Observer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Cms\Model\Page;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\CustGroupCatPermissions\Model\CmsPagePermission\Applier;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class CmsPageRender
 * @package Aheadworks\CustGroupCatPermissions\Observer
 */
class CmsPageRender implements ObserverInterface
{
    /**
     * Redirected flag
     */
    const REDIRECTED_FLAG = '_redirected';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Applier
     */
    private $permissionApplier;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param Config $config
     * @param Applier $permissionApplier
     * @param ManagerInterface $manager
     */
    public function __construct(
        Config $config,
        Applier $permissionApplier,
        ManagerInterface $manager
    ) {
        $this->config = $config;
        $this->permissionApplier = $permissionApplier;
        $this->messageManager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return $this;
        }

        /** @var Page $cmsPage */
        $cmsPage = $observer->getEvent()->getPage();
        if ($this->isNeedToApplyPermissions($cmsPage)) {
            $this->permissionApplier->applyForCmsPage($cmsPage);
            if ($cmsPage->getData(Applier::HIDE_PAGE)) {
                $this->performRedirect($observer);
            }
        }

        return $this;
    }

    /**
     * @param Page $cmsPage
     * @return bool
     */
    private function isNeedToApplyPermissions($cmsPage)
    {
        return $cmsPage->getIdentifier() != $this->config->getNoRouteCmsPageIdentifier();
    }

    /**
     * Perform redirect
     *
     * @param Observer $observer
     * @throws NotFoundException
     */
    private function performRedirect(Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        $message = __('Sorry, you are not allowed to view this page.');
        $url = $this->config->getCmsPageBrowsingRedirectUrl();

        if ($url) {
            $observer->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
            $request->setPathInfo($request->getPathInfo() . self::REDIRECTED_FLAG);
            $this->messageManager->addErrorMessage($message);
        } else {
            throw new NotFoundException($message);
        }
    }
}
