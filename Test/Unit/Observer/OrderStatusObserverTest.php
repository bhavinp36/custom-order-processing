<?php

namespace Vendor\CustomOrderProcessing\Test\Unit\Observer;

use PHPUnit\Framework\TestCase;
use Vendor\CustomOrderProcessing\Observer\OrderStatusObserver;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vendor\CustomOrderProcessing\Model\StatusLogFactory;
use Vendor\CustomOrderProcessing\Model\StatusLog;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Psr\Log\LoggerInterface;

class OrderStatusObserverTest extends TestCase
{
    /**
     * @var StatusLogFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logFactoryMock;

    /**
     * @var TransportBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $transportBuilderMock;

    /**
     * @var StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;

    /**
     * @var DateTime|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dateTimeMock;

    /**
     * @var ShipmentSender|\PHPUnit\Framework\MockObject\MockObject
     */
    private $shipmentSenderMock;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var OrderStatusObserver
     */
    private $observerClass;

    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderMock;

    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $shipmentMock;

    protected function setUp(): void
    {
        $this->logFactoryMock = $this->createMock(StatusLogFactory::class);
        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->shipmentSenderMock = $this->createMock(ShipmentSender::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->observerClass = new OrderStatusObserver(
            $this->logFactoryMock,
            $this->transportBuilderMock,
            $this->storeManagerMock,
            $this->dateTimeMock,
            $this->shipmentSenderMock,
            $this->loggerMock
        );
    }

    public function testExecuteDoesNothingIfStatusIsNotShipped(): void
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getStatus')->willReturn('processing');
        $orderMock->method('getOrigData')->willReturn('processing'); // no change

        $observerMock = $this->createMock(Observer::class);
        $observerMock->method('getEvent')->willReturn(
            new class($orderMock) {
                /** @var Order */
                private $order;
                public function __construct($order)
                {
                    $this->order = $order;
                }
                public function getData($key)
                {
                    return $key === 'order' ? $this->order : null;
                }
            }
        );

        $this->shipmentSenderMock->expects($this->never())->method('send');

        $this->observerClass->execute($observerMock);
    }

    public function testExecuteSendsShipmentEmailIfStatusIsShipped(): void
    {
        $shipmentMock = $this->createMock(\Magento\Sales\Model\Order\Shipment::class);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getStatus')->willReturn('shipped');
        $orderMock->method('getOrigData')->willReturn('processing');
        $orderMock->method('getId')->willReturn(123);
        $orderMock->method('getShipmentsCollection')->willReturn([$shipmentMock]);

        $logMock = $this->getMockBuilder(StatusLog::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setData', 'save'])
            ->getMock();

        $logMock->expects($this->once())->method('setData')->with($this->arrayHasKey('order_id'));
        $logMock->expects($this->once())->method('save');

        $this->logFactoryMock->method('create')->willReturn($logMock);

        $this->shipmentSenderMock
            ->expects($this->once())
            ->method('send')
            ->with($shipmentMock);

        $this->dateTimeMock->method('gmtDate')->willReturn('2024-06-01 12:00:00');

        $observerMock = $this->createMock(Observer::class);
        $observerMock->method('getEvent')->willReturn(
            new class($orderMock) {
                /** @var \Magento\Sales\Model\Order */
                private $order;
                public function __construct($order)
                {
                    $this->order = $order;
                }
                public function getData($key)
                {
                    return $key === 'order' ? $this->order : null;
                }
            }
        );

        $this->observerClass->execute($observerMock);
    }
}
