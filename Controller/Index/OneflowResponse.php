<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Df\Framework\W\Result\Json as J;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Convert\Order as OC;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item as SI;
use Magento\Sales\Model\Order\Shipment\Track;
class OneflowResponse extends \Magento\Framework\App\Action\Action {
	/**          
	 * 2018-12-28
	 * @see \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @return J
	 * @throws LE
	 */
	function execute() {return ikf_endpoint(function() {
		$json = file_get_contents('php://input');
		ikf_logger('oneflow_status')->info($json);
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
			$oc = df_new_om(OC::class);            /** @var OC $oc */
			$shipment = $oc->toShipment($o);       /** @var Shipment $shipment */
			foreach ($o->getAllItems() AS $oi) {   /** @var OI $oi */
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
			$track = df_new_om(Track::class); /** @var Track $track */
			$tn = dfa($req, 'TrackingNumber');
			$trackingResult = (strlen($tn) > 3) ? $tn : __('Untracked');
			$track->setNumber($trackingResult);
			$track->setTrackNumber($trackingResult);
			$carrier = null;
			if ($tn !== null && isset($req['Carrier'])) {
				$carrier = $req['Carrier'];
				$trackingUrl = null;
				if (preg_match('/royalmail/i', $carrier)) {
					$trackingUrl = "https://www.royalmail.com/track-your-item#/tracking-results/$tn";
				}
				else if (preg_match('/dhl/i', $carrier)) {
					$trackingUrl = "https://www.dhl.co.uk/content/gb/en/express/tracking.shtml?brand=DHL&AWB=$tn";
				}
				else if (preg_match('/fedex/i', $carrier)) {
					$trackingUrl = "https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=$tn";
				}
				else if (preg_match('/dpd/i', $carrier)) {
					$trackingUrl = 'https://www.dpd.co.uk/content/how-can-we-help/index.jsp';
				}
				else if (preg_match('/usps/i', $carrier)) {
					$trackingUrl = "https://tools.usps.com/go/TrackConfirmAction?tLabels=$tn";
					$track->setCarrierCode($carrier);
					$track->setTitle('United States Postal Service');
				}
				else if (preg_match('/ups/i', $carrier)) {
					$trackingUrl = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=$tn";
					$track->setCarrierCode($carrier);
					$track->setTitle('United Parcel Service');
				}
				$trackingFinalUrl = $trackingUrl . $tn;
				if ($trackingUrl) {
					$track->setTrackingUrl($trackingFinalUrl);
					$track->setTracking($trackingFinalUrl);
					$track->setUrl($trackingFinalUrl);
				}
			}
			if (!($carrier = dfa($req, 'Carrier'))) { /** @var string|null $carrier */
				$track->setCarrierCode('OneFlow');
				$track->setDescription('OneFlow');
				$trackingTitle = (substr($tn, 0, 2) == 'JD') ? 'DHL' : 'Royal Mail';
				$track->setTitle($trackingTitle);
			}
			else {
				// 2019-01-05 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// «Improve MediaClip module for Magento 2: add USPS shipping tracking URLs to emails»
				// https://www.upwork.com/ab/f/contracts/21337553
				$track->setDescription($carrier);
				if (df_contains($carrier, 'ups')) {
					$track->setCarrierCode($carrier);
					$track->setTitle('United Parcel Service');
				}
				else {
					$track->setCarrierCode($carrier);
					$track->setTitle('United States Postal Service');
				}
			}
			/**
			 * 2019-01-30 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/blob/4d3325d8/Controller/Index/OneflowResponse.php#L48-L52
			 * «The US shipping emails are working well now.
			 * But since this was implemented the UK store shipping emails have stopped working.
			 * I asked my printer and they said that when they post back they now get the response:
			 * "Cannot save track:\nNumber is a required field".
			 * The problem is not all the UK orders have a tracking number, many are sent untracked.
			 * Would you be able to update the code to get the UK shipping emails working again please?»
			 * https://www.upwork.com/messages/rooms/room_759684bcafe746240e5c091d3745e787/story_57747a8340bd7d33c86a4247e893e150
			 */
			if (!$track->getNumber()) {
				$track->setNumber('N/A');
			}
			$shipment->addTrack($track);
			$shipment->save();
			$o->save();
			df_mail_shipment($shipment);
			$shipment->save();
		}
	});}
}
