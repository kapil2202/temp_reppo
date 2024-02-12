<?php
namespace Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class Delete
 * @package Aheadworks\CustGroupCatPermissions\Controller\Adminhtml\Bulk
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_CustGroupCatPermissions::bulk_delete';

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
            ->getConfig()->getTitle()->prepend(__('Delete Permissions in Bulk'));

        return $resultPage;
    }
}
