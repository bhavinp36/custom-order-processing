<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Vendor\CustomOrderProcessing\Model\StatusLogFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Cache\Tag\Resolver;

class OrderStatusObserver implements ObserverInterface
{
    protected const CACHE_TAG = 'vendor_order_status_log';
    protected const CACHE_KEY_PREFIX = 'status_log_order_';

    /**
     * @var StatusLogFactory
     */
    protected $logFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Constructor for OrderStatusObserver.
     *
     * Initializes dependencies for logging and email notifications.
     *
     * @param StatusLogFactory      $logFactory
     * @param TransportBuilder      $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param DateTime              $dateTime
     * @param ShipmentSender        $shipmentSender
     * @param LoggerInterface       $logger
     * @param CacheInterface        $cache
     */
    public function __construct(
        StatusLogFactory $logFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        ShipmentSender $shipmentSender,
        LoggerInterface $logger,
        CacheInterface $cache
    ) {
        $this->logFactory = $logFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->shipmentSender = $shipmentSender;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * Observer execution for order status change.
     *
     * Logs order status changes and sends shipment email if applicable.
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getData('order');
        $origStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();

        $cacheKey = self::CACHE_KEY_PREFIX . $order->getId();

        if ($origStatus !== $newStatus) {
            $log = $this->logFactory->create();

            $log->setData(
                [
                'order_id' => $order->getId(),
                'old_status' => $origStatus,
                'new_status' => $newStatus,
                'created_at' => $this->dateTime->gmtDate()
                ]
            );
            $log->save();

            // Invalidate cache
            $this->cache->remove($cacheKey);

            // Store updated status in cache (optional)
            $this->cache->save($newStatus, $cacheKey, [self::CACHE_TAG], 3600);

            // Send shipment email if order is marked complete or shipped
            if ($newStatus === Order::STATE_COMPLETE || $newStatus === 'shipped') {
                try {
                    $shipments = $order->getShipmentsCollection();
                    foreach ($shipments as $shipment) {
                        $this->shipmentSender->send($shipment);
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Failed to send shipment email: ' . $e->getMessage());
                }
            }
        }
    }
}
