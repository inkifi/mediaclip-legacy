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
		$l = new \Zend\Log\Logger();
		$l->addWriter($writer);
		$json = file_get_contents('php://input');
		$l->info($json);
		/**
		 * 2018-12-28 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * "Improve MediaClip module for Magento 2: handle shipping notifications for the US store":
		 * https://www.upwork.com/ab/f/contracts/21253740
		 * https://www.upwork.com/messages/rooms/room_33abff92763bac6bac92a394ada0f09c/story_54a27db7705e1c75e5964af8bad331dc
		 * https://www.upwork.com/messages/rooms/room_8f56da8c65d755371bad23ddebe0721a
		 * A request for the US store is invalid, e.g.:
		 * {
		 *		"TimeStamp": "2018-12-08T05:52:56.584Z",
		 *		"SourceOrderId": "53297",
		 *		"SourceItemId": "",
		 *		"SourceShipmentId": "",
		 *		"ShipmentIndex": "0",
		 *		"TrackingNumber": "9405515901453214355474",
		 *		"Carrier": shqcustomc_po_pm
		 *		"OrderStatus": "Shipped"
		 *	}
		 * I make it valid.
		 */
		/*if (df_my_local()) {
			$json = '{
				"TimeStamp": "2018-12-08T05:52:56.584Z",
				"SourceOrderId": "53297",
				"SourceItemId": "",
				"SourceShipmentId": "",
				"ShipmentIndex": "0",
				"TrackingNumber": "9405515901453214355474",
				"Carrier": shqcustomc_po_pm
				"OrderStatus": "Shipped"
			}';
		} */
		$json = preg_replace('#"Carrier":\s*(\w+)#', '"Carrier": "$1",', $json);
		$req = json_decode($json, true);
		/**
		 * 2019-01-03 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * As you can see in the example above, for the US store, the status is «Shipped», not «shipped».
		 * https://www.upwork.com/messages/rooms/room_759684bcafe746240e5c091d3745e787/story_243ee6d47456e76732d9bb4c80ae869e
		 * So I have added @uses strtolower() now.
		 */
		if (!empty($req) && isset($req['OrderStatus']) && 'shipped' === strtolower($req['OrderStatus'])) {
			$oid = intval($req['SourceOrderId']); /** @var int $oid */
			$o = df_new_om(O::class)->load($oid); /** @var O $o */
			if (!$o->canShip()) {
				throw new LE( __("You can't create an shipment."));
			}
			$oc = df_new_om(OC::class); /** @var OC $oc */
			$shipment = $oc->toShipment($o); /** @var Shipment $shipment */
			foreach ($o->getAllItems() AS $oi) { /** @var OI $oi */
				if (!$oi->getQtyToShip() || $oi->getIsVirtual()) {
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
				if (!($carrier = dfa($req, 'Carrier'))) { /** @var string|null $carrier */
					$track->setCarrierCode('OneFlow');
					$track->setDescription('OneFlow');
					$track->setTitle('Royal Mail');
				}
				else {
					// 2019-01-05 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					// «Improve MediaClip module for Magento 2: add USPS shipping tracking URLs to emails»
					// https://www.upwork.com/ab/f/contracts/21337553
					$track->setDescription($carrier);
					$tn = dfa($req, 'TrackingNumber'); /** @var string $tn */
					$track->setNumber($tn);
					if (df_contains($carrier, 'ups')) {
						//track->setCarrierCode('ups');
						$track->setCarrierCode($carrier);
						$track->setTitle('United Parcel Service');
					}
					else {
						//$track->setCarrierCode('usps');
						$track->setCarrierCode($carrier);
						$track->setTitle('United States Postal Service');
					}
				}
				$shipment->addTrack($track);
				$shipment->save();
				$o->save();
				$notifier = df_new_om(ShipmentNotifier::class); /** @var ShipmentNotifier $notifier */
				$notifier->notify($shipment);
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
