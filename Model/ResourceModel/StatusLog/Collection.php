<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model\ResourceModel\StatusLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection class for StatusLog entries.
 *
 * Represents a collection of records from the vendor_customorder_status_log table.
 */
class Collection extends AbstractCollection
{
    /**
     * Define model and resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Vendor\CustomOrderProcessing\Model\StatusLog::class,
            \Vendor\CustomOrderProcessing\Model\ResourceModel\StatusLog::class
        );
    }
}
