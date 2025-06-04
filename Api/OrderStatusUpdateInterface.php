<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api;

interface OrderStatusUpdateInterface
{
    /**
     * Update order status via order increment ID
     *
     * @param  string $orderIncrementId
     * @param  string $newStatus
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function updateStatus(string $orderIncrementId, string $newStatus);
}
