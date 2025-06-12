<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class StatusLog
 *
 * Model for storing order status change logs.
 */
class StatusLog extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag for StatusLog entries
     */
    public const CACHE_TAG = 'vendor_order_status_log';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = 'vendor_order_status_log';

    /**
     * Initialize StatusLog model
     */
    protected function _construct(): void
    {
        $this->_init(\Vendor\CustomOrderProcessing\Model\ResourceModel\StatusLog::class);
    }

    /**
     * Get identities for cache tagging
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
