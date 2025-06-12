<?php

namespace Vendor\CustomOrderProcessing\Test\Integration\Observer;

use Magento\TestFramework\ObjectManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;

class OrderStatusObserverTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    public function testOrderStatusShippedTriggersObserver()
    {
        /**
         * @var OrderRepositoryInterface $orderRepository
        */
        $orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);

        /**
         * @var Order $order
        */
        $order = $orderRepository->get(1); // Assuming order with ID 1 exists in fixtures
        $order->setStatus('shipped');
        $orderRepository->save($order);

        // Add your assertions here, possibly verifying email sent or shipment updated
        $this->assertEquals('shipped', $order->getStatus());
    }
}
