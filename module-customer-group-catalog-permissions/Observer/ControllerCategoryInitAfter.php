<?php
namespace Aheadworks\CustGroupCatPermissions\Observer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class ControllerCategoryInitAfter
 * @package Aheadworks\CustGroupCatPermissions\Observer
 */
class ControllerCategoryInitAfter implements ObserverInterface
{
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

        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        $this->permissionApplier->applyForCategory($category);
        if (!$category->getIsActive()) {
            $this->performRedirect($observer);
        }

        return $this;
    }

    /**
     * Perform redirect
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    private function performRedirect(Observer $observer)
    {
        $message = __('Sorry, you are not allowed to view this category.');
        $url = $this->config->getCatalogElementBrowsingRedirectUrl();

        if ($url) {
            $observer->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
        }

        throw new LocalizedException($message);
    }
}
