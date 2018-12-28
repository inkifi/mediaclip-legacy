<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use pwinty\PhpPwinty;

class PwintyOrderStatusUpdate extends Action
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
    function __construct(
        Context $context,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->trackFactory = $trackFactory;
        parent::__construct($context);
 
    }
 
    function execute()
    {
        
        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pwinty_orders_status.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        //$json = $_POST;
        $logger->info($_POST);
        //$obj = json_decode($json, true);
        die('45');*/
        //$logger->info('Array Log'.print_r($myArrayVar, true));
        //Mage::log($json, null, 'mediaclip_orders_download_shipment_status.log');
    $json ='{"id":971961,"status":"Complete","shipments":[{"status":"shipped","items":[6468905],"trackingNumber":"MQ121286142GB","trackingUrl":"http://www.royalmail.com/portal/rm/track?trackNumber=MQ121286142GB"}],"environment":"live","timestamp":"2017-12-11T17:39:00.2368387Z"}';
        $obj = json_decode($json, true);
       echo "<pre>"; print_r($obj); 
        if (isset($obj['shipments'])) {
            foreach ($obj['shipments'] as $value) {
                if ($value['status'] == 'shipped') {
                   
                    $pwintyorderId = $obj['id'] ;
                    $mediaclipOrderModel = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Orders');
                    $mediaclipOrderModelCollection = $mediaclipOrderModel->getCollection();
                    $mediaclipOrder = $mediaclipOrderModelCollection->addFieldToFilter('pwinty_order_id', array('eq' => '682012'));
                    //print_r($mediaclipOrder->getData()[0]['magento_order_id']);
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load(
						// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
						// «Modify orders numeration for Mediaclip»
						// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
						ikf_eti($mediaclipOrder->getData()[0]['magento_order_id'])
					);
                    // Check if order can be shipped or has already shipped

                    if (! $order->canShip()) {
                        throw new \Magento\Framework\Exception\LocalizedException( __('You can\'t create an shipment.'));
                    }

                    foreach ($mediaclipOrder as $key => $trackingValue) {

                        $trackingValue->setTrackingNumber($value['trackingNumber']);
                        $trackingValue->setTrackingUrl($value['trackingUrl']);
                        $trackingValue->save();
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
                        $track->setNumber($value['trackingNumber']);
                        $track->setCarrierCode('Pwinty');
                        $track->setTitle('Pwinty');
                        $track->setDescription("Pwinty");
                        $track->setUrl($value['trackingUrl']);
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
        }
        //print_r($obj);
        die('57');
    }
    /*function FunctionName($value='')
    {
        // Load the order increment ID
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementID($incrementid);

        // OR
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')
            ->loadByAttribute('increment_id', '000000001');


        //load by order 
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')
            ->load('1');

        // Check if order can be shipped or has already shipped
        if (! $order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                            __('You can\'t create an shipment.')
                        );
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
    }*/
}
