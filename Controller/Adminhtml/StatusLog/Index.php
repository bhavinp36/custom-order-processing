<?php
namespace Vendor\CustomOrderProcessing\Controller\Adminhtml\StatusLog;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Action\Context $context
     * @param PageFactory    $resultPageFactory
     */
    public function __construct(Action\Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute method to render the page for Order Status Logs.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vendor_CustomOrderProcessing::status_log');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Status Logs'));
        return $resultPage;
    }
}
