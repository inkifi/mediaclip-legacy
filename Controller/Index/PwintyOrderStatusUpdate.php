<?php 
namespace Mangoit\MediaclipHub\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Convert\Order as Converter;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item as SI;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Shipping\Model\ShipmentNotifier;
use Mangoit\MediaclipHub\Model\Orders as mOrders;
use pwinty\PhpPwinty;
class PwintyOrderStatusUpdate extends Action {
    function execute() {
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
                    $mOrders = df_new_om(mOrders::class); /** @var mOrders $mOrders */
                    $mOrdersC = $mOrders->getCollection();
                    $mOrder = $mOrdersC->addFieldToFilter('pwinty_order_id', ['eq' => '682012']);
                    //print_r($mediaclipOrder->getData()[0]['magento_order_id']);
                    $order = df_new_om(O::class)->load(
						// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
						// «Modify orders numeration for Mediaclip»
						// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
						ikf_eti($mOrder->getData()[0]['magento_order_id'])
					); /** @var O $order */
                    if (!$order->canShip()) {
                        throw new LE(__('You can\'t create an shipment.'));
                    }
                    foreach ($mOrder as $key => $trackingValue) {
                        $trackingValue->setTrackingNumber($value['trackingNumber']);
                        $trackingValue->setTrackingUrl($value['trackingUrl']);
                        $trackingValue->save();
                    }
                    $converter = df_new_om(Converter::class); /** @var Converter $converter */
                    $shipment = $converter->toShipment($order); /** @var Shipment $shipment */
                    foreach ($order->getAllItems() as $oi) { /** @var OI $oi */
                        if ($oi->getQtyToShip() && !$oi->getIsVirtual()) {
							$qtyShipped = $oi->getQtyToShip();
							$si = $converter->itemToShipmentItem($oi); /** @var SI $si */
							$si->setQty($qtyShipped);
							$shipment->addItem($si);
                        }
                    }
                    $shipment->register();
                    $shipment->getOrder()->setIsInProcess(true);
                    try {
                        $track = df_new_om(Track::class); /** @var Track $track */
                        $track->setCarrierCode('Pwinty');
                        $track->setDescription("Pwinty");
                        $track->setNumber($value['trackingNumber']);
                        $track->setTitle('Pwinty');
                        $track->setUrl($value['trackingUrl']);
                        $shipment->addTrack($track);
                        $shipment->save();
                        $shipment->getOrder()->save();
                        df_new_om(ShipmentNotifier::class)->notify($shipment);
                        $shipment->save();
                    } catch (\Exception $e) {
                        throw new LE(__($e->getMessage()));
                    }
                }
            }
        }
        die('57');
    }
    /**
	 * 2019-02-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Port a Pwinty's integration from Magento 1 to Magento 2»
	 * https://www.upwork.com/ab/f/contracts/21642484
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/10
	 * It was a disabled code here:
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/blob/cd2b7930/Controller/Index/PwintyOrderStatusUpdate.php#L110-L171
	 */
}