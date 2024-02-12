<?php
namespace Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class Update
 *
 * @package Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk
 */
class Update extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_CustGroupCatPermissions::bulk_update';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_CustGroupCatPermissions::home')
            ->getConfig()->getTitle()->prepend(__('Update Permissions in Bulk'));

        return $resultPage;
    }
}
