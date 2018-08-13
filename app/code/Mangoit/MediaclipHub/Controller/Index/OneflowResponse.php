<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class OneflowResponse extends Action
{
        /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $trackFactory;
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->trackFactory = $trackFactory;
        parent::__construct($context);
 
    }
 
    public function execute()
    {
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/oneflow_status.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $json = file_get_contents('php://input');
        $logger->info($json);
        /*$json = '{"TimeStamp": "2018-04-06T10:20:50.506Z","SourceOrderId": "39477","SourceShipmentId": "","ShipmentIndex": "0","TrackingNumber": "","OrderStatus": "shipped"}';*/
        $obj = json_decode($json, true);
        if (!empty($obj)) {

            if (isset($obj['OrderStatus']) && $obj['OrderStatus'] == 'shipped') {
                    $orderId = $obj['SourceOrderId'];
                    
                    if ($obj['TrackingNumber'] != '') {
                        $trackingNumber = $obj['TrackingNumber'];
                    }else{
                        $trackingNumber = 'N/A';
                    }
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
                    // Check if order can be shipped or has already shipped

                    if (! $order->canShip()) {
                        throw new \Magento\Framework\Exception\LocalizedException( __('You can\'t create an shipment.'));
                    }

                    // Initialize the order shipment object
                    $convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
                    $shipment = $convertOrder->toShipment($order);

                    // Loop through order items
                    foreach ($order->getAllItems() AS $orderItem) {
                        // Check if order item has qty to ship or is virtual
                        if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                            continue;
                        }

                        $qtyShipped = $orderItem->getQtyToShip();

                        // Create shipment item with qty
                        $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                        
                        // Add shipment item to shipment
                        $shipment->addItem($shipmentItem);
                    }

                    // Register shipment
                    $shipment->register();

                    $shipment->getOrder()->setIsInProcess(true);

                    try {
                        $track = $this->trackFactory->create();
                        $track->setNumber($trackingNumber);
                        $track->setCarrierCode('OneFlow');
                        $track->setTitle('Royal Mail');
                        $track->setDescription("OneFlow");
                        //$track->setUrl($value['trackingUrl']);
                        $shipment->addTrack($track);
                        // Save created shipment and order
                        $shipment->save();
                        $shipment->getOrder()->save();
                        // Send email
                        $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                            ->notify($shipment);
                        $shipment->save();
                    } catch (\Exception $e) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                                        __($e->getMessage())
                                    );
                    }
                }
        }
        die('45');
        //$logger->info('Array Log'.print_r($myArrayVar, true));
        //Mage::log($json, null, 'mediaclip_orders_download_shipment_status.log');
    }
}
