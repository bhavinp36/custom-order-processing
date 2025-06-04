<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Vendor\CustomOrderProcessing\Api\OrderStatusUpdateInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

/**
 * Class OrderStatusUpdate
 *
 * Handles order status updates through API.
 */
class OrderStatusUpdate implements OrderStatusUpdateInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Constructor
     *
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Update order status by increment ID
     *
     * @param  string $orderIncrementId
     * @param  string $newStatus
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws LocalizedException
     */
    public function updateStatus(string $orderIncrementId, string $newStatus)
    {
        $order = $this->orderRepository->get($orderIncrementId);

        if (!$order->getEntityId()) {
            throw new LocalizedException(__('Order not found.'));
        }

        // Validate if status transition is allowed
        if (!$order->canHold() && $order->getStatus() === $newStatus) {
            throw new LocalizedException(__('This status update is not allowed.'));
        }

        $order->setStatus($newStatus);
        $order->addStatusHistoryComment(__('Status updated via API to %1', $newStatus));
        $this->orderRepository->save($order);

        return $order;
    }
}
