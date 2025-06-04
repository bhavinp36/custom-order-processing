<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class StatusLog
 *
 * Model for storing order status change logs.
 */
class StatusLog extends AbstractModel
{
    /**
     * Initialize StatusLog model
     */
    protected function _construct()
    {
        $this->_init(\Vendor\CustomOrderProcessing\Model\ResourceModel\StatusLog::class);
    }
}
