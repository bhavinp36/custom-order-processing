<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model for order status log.
 *
 * Handles DB operations for the vendor_customorder_status_log table.
 */
class StatusLog extends AbstractDb
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('vendor_customorder_status_log', 'entity_id');
    }
}
