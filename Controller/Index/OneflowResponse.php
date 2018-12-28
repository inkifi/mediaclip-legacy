<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Convert\Order as OC;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item as SI;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Shipping\Model\ShipmentNotifier;
class OneflowResponse extends \Magento\Framework\App\Action\Action {
	/**          
	 * 2018-12-28
	 * @throws LE
	 */
	function execute() {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/oneflow_status.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$json = file_get_contents('php://input');
		$logger->info($json);
		/*$json = '{"TimeStamp": "2018-04-06T10:20:50.506Z","SourceOrderId": "39477","SourceShipmentId": "","ShipmentIndex": "0","TrackingNumber": "","OrderStatus": "shipped"}';*/
		$req = json_decode($json, true);
		if (!empty($req) && isset($req['OrderStatus']) && 'shipped' === $req['OrderStatus']) {
			$oid = intval($req['SourceOrderId']); /** @var int $oid */
			$trackingNumber = $req['TrackingNumber'] ?: 'N/A'; /** @var string $trackingNumber */
			$o = df_new_om(O::class)->load($oid); /** @var O $o */
			if (!$o->canShip()) {
				throw new LE( __("You can't create an shipment."));
			}
			$oc = df_new_om(OC::class); /** @var OC $oc */
			$shipment = $oc->toShipment($o); /** @var Shipment $shipment */
			foreach ($o->getAllItems() AS $oi) { /** @var OI $oi */
				if (! $oi->getQtyToShip() || $oi->getIsVirtual()) {
					continue;
				}
				$qtyShipped = $oi->getQtyToShip();
				$si = $oc->itemToShipmentItem($oi); /** @var SI $si */
				$si->setQty($qtyShipped);
				$shipment->addItem($si);
			}
			$shipment->register();
			$o['is_in_process'] = true;
			try {
				$track = df_new_om(Track::class); /** @var Track $track */
				$track->setCarrierCode('OneFlow');
				$track->setDescription('OneFlow');
				$track->setNumber($trackingNumber);
				$track->setTitle('Royal Mail');
				//$track->setUrl($value['trackingUrl']);
				$shipment->addTrack($track);
				$shipment->save();
				$o->save();
				df_new_om(ShipmentNotifier::class)->notify($shipment);
				$shipment->save();
			} 
			catch (\Exception $e) {
				throw new LE(__($e->getMessage()));
			}
		}
		die('45');
		//$logger->info('Array Log'.print_r($myArrayVar, true));
		//Mage::log($json, null, 'mediaclip_orders_download_shipment_status.log');
	}
}
