<?php
namespace Vendor\CustomOrderProcessing\Controller\Adminhtml\StatusLog;

use Magento\Backend\App\Action;
use Vendor\CustomOrderProcessing\Model\ResourceModel\StatusLog\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;

class MassDisable extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDisable constructor.
     *
     * @param Action\Context    $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute mass disable action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $updated = 0;

        foreach ($collection as $item) {
            $item->setIsEnabled(0);
            $item->save();
            $updated++;
        }

        $this->messageManager->addSuccessMessage(__('%1 record(s) disabled.', $updated));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check if the user has permission to perform the mass disable action.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_CustomOrderProcessing::massdisable');
    }
}
