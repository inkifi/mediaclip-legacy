<?php
namespace Mangoit\MediaclipHub\Observer;
use Magento\Catalog\Model\Product as P;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\Country;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IAttributeSet;
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface as IOrderRepository;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\OrderRepository;
use Mangoit\MediaclipHub\Helper\Data as mHelper;
use Mangoit\MediaclipHub\Model\Supplier as mSupplier;
/**
 * 2018-06-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
 * "Prevent the «Pending Payment» orders from being sent to MediaClip Photobook in my Magento 2 store":
 * https://www.upwork.com/ab/f/contracts/20288301
 */
final class CheckoutSuccess implements ObserverInterface {
	/**
	 * 2018-06-26
	 * @override
	 * @see ObserverInterface::execute()
	 * What events are triggered on an order placement? https://mage2.pro/t/3573
	 * @param Observer $ob
	 */
	function execute(Observer $ob) {
		$om = OM::getInstance(); /** @var OM $om */
		$r = $om->get(IOrderRepository::class); /** @var IOrderRepository|OrderRepository $r */
		$o = $r->get($ob['order_ids'][0]); /** @var O $o */
		if ('pending_payment' !== $o->getStatus()) {
			self::post($o);
		}
	}

	/**
	 * 2018-06-26
	 * @used-by execute()
	 * @param O $o
	 */
    static function post(O $o) {
        $om = OM::getInstance(); /** @var OM $om */
		// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Modify orders numeration for Mediaclip»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
        $oid = ikf_ite($o->getId()); /** @var int $oid */
		$supplier = $om->create(mSupplier::class);
		$supplierDataCollection = $supplier->getCollection()->getData();
		$supplierData = [];
		if ($supplierDataCollection) {
			foreach ($supplierDataCollection as $value) {
				$supplierData[$value['id']]['domain'] = $value['domain'];
				$supplierData[$value['id']]['value'] = $value['value'];
			}
		}
		$i = 1;
		$order_item_details = [];
		// 2018-07-04 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// The `$item_supplier_id` variable should be defined outside of the loop
		// because it is used outside of the loop below.
		$item_supplier_id = [];
		foreach ($o->getAllItems() as $oi) { /** @var OI $oi */
			if ($oi->getMediaclipProjectId()) {
				$item_supplier_id = [];
				$p = $om->create(P::class)->load($oi->getProductId());  /** @var P $p */
				$pid = $oi->getProductId();
				$product_desc = $p->getShortDescription();
				$item_id['buyerPartId'] = $pid;   //{YOUR-STORE-PRODUCT-ID}
				$item_id['supplierPartAuxiliaryId'] = $oi->getMediaclipProjectId();   //{HUB-PROJECT-ID}
				if ($p->getMediaclipProductSupplier()) {
					if ($supplierData) {
						$supplierId = $p->getMediaclipProductSupplier();
						$item_supplier_id['domain'] = $supplierData[$supplierId]['domain'];
						$item_supplier_id['value'] = $supplierData[$supplierId]['value'];
						$item_detail[$item_supplier_id['domain']] = $item_supplier_id['value'];
					}
				}
				$item_detail['unitPrice']['money']['currency'] = $o->getOrderCurrencyCode();
				$item_detail['unitPrice']['money']['value'] = $oi->getPrice();
				$item_detail['description'] = $product_desc;
				$item['lineNumber'] = $i;
				$item['itemId'] = $item_id;
				$attributeSet = $om->create(IAttributeSet::class); /** @var IAttributeSet $attributeSet */
				$attributeSetRepository = $attributeSet->get($p->getAttributeSetId());
				$attribute_set_name = $attributeSetRepository->getAttributeSetName();
				$item_quantity = 0;
				if ('Photobook' === $attribute_set_name) {
					$item_quantity = (int)$oi->getQtyOrdered();
				}
				else if ('Print' === $attribute_set_name) {
					$item_quantity = 1;
				}
				else if ('Gifting' === $attribute_set_name) {
					$item_quantity = (int)$oi->getQtyOrdered();
				}
				if (!empty($item_supplier_id)) {
					$item['supplierId'] = $item_supplier_id;
				}
				$item['quantity'] = $item_quantity;
				$item['itemDetail'] = $item_detail;
				$order_item_details[] = $item;
				$i++;
			}
		}
		if ($order_item_details) {
			$order_date = $o->getCreatedAt();
			$shipping_address = $o->getShippingAddress()->getData();
			$order_ship_to = [];
			if ($shipping_address) {
				$firstname = (!empty($shipping_address['firstname'])) ? $shipping_address['firstname'] : '';
				$middlename = (!empty($shipping_address['middlename'])) ? " ".$shipping_address['middlename'] : '';
				$lastname = (!empty($shipping_address['lastname'])) ? " ".$shipping_address['lastname'] : '';
				$postalAddress['deliverTo'] = $firstname.$middlename.$lastname;
				$postalAddress['street'] = $shipping_address['street'];
				$postalAddress['city'] = $shipping_address['city'];
				$postalAddress['state'] = $shipping_address['region'];
				$postalAddress['postalCode'] = $shipping_address['postcode'];
				$countryCode = $shipping_address['country_id'];
				$country = $om->create(Country::class)->loadByCode($countryCode); /** @var Country $country */
				$countryName = $country->getName();
				$postalAddress['country']['isoCountryCode'] = $countryCode;
				$postalAddress['country']['value'] = $countryName;
				$email['value'] = $shipping_address['email'];
				$phone['number'] = $shipping_address['telephone'];
				$order_ship_to['address']['postalAddress'] = $postalAddress;
				$order_ship_to['address']['email'] = $email;
				$order_ship_to['address']['phone'] = $phone;
			}
			/**
			 * 2018-07-04 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * The previous code was: `if (!empty($item_supplier_id)) {`
			 * This code is wrong because if `$item_supplier_id` is an empty array
			 * then `!empty($item_supplier_id)` returns `true`,
			 * and it leads to the error «Notice: Undefined index: supplierId» on the next line.
			 */
			if ($item_supplier_id) {
				if (
					// 2018-07-04 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					isset($order_item_details[0]['supplierId'])
					&& $order_item_details[0]['supplierId']['value'] == 'OneFlowCloud'
				) {
					$carrierIdentifier_carrier['domain'] = 'alias';
					$carrierIdentifier_carrier['value'] = $o->getShippingMethod();
					$carrierIdentifier[] = $carrierIdentifier_carrier;
					$order_ship_to['carrierIdentifier'] = $carrierIdentifier;
				}
			}
			$billing_address = $o->getBillingAddress()->getData();
			$order_bill_to = array();
			if ($billing_address) {
				$firstname = (!empty($billing_address['firstname'])) ? $billing_address['firstname'] : '';
				$middlename = (!empty($billing_address['middlename'])) ? " ".$billing_address['middlename'] : '';
				$lastname = (!empty($billing_address['lastname'])) ? " ".$billing_address['lastname'] : '';
				$postalAddress['deliverTo'] = $firstname.$middlename.$lastname;
				$postalAddress['street'] = $billing_address['street'];
				$postalAddress['city'] = $billing_address['city'];
				$postalAddress['state'] = $billing_address['region'];
				$postalAddress['postalCode'] = $billing_address['postcode'];
				$countryCode = $billing_address['country_id'];
				$country = $om->create(Country::class)->loadByCode($countryCode);
				$countryName = $country->getName();
				$postalAddress['country']['isoCountryCode'] = $countryCode;
				$postalAddress['country']['value'] = $countryName;
				$email['value'] = $billing_address['email'];
				$phone['number'] = $billing_address['telephone'];
				$order_bill_to['address']['postalAddress'] = $postalAddress;
				$order_bill_to['address']['email'] = $email;
				$order_bill_to['address']['phone'] = $phone;
			}
			$order_shipping['money']['currency'] = $o->getOrderCurrencyCode();
			$order_shipping['money']['value'] = $o->getShippingAmount();
			$order_shipping['description']['value'] = $o->getShippingDescription();
			if ($o->getCustomerId() === NULL){
				$customer_id = $o->getCustomerId();
			}
			else {
				$session = $om->create(Session::class);
				if ($session->getMediaClipUserId()) {
					$customer_id = $session->getMediaClipUserId();
				}
				else {
					$op = $o->getPayment(); /** @var OP $op */
					$customer_id = $op->getAdditionalInformation('df_mediaclip_customer_id');					        if (!$customer_id) {
						$customer_id = $o->getCustomerName();
					}
				}
			}
			$contact_details['idReference']['identifier'] = $customer_id;
			$contact_details['idReference']['domain'] = 'storeUserId';
			$order_contact = $contact_details;
			$order_details['orderID'] = $oid;
			$order_details['orderDate'] = $order_date;
			$order_details['shipTo'] = $order_ship_to;
			$order_details['billTo'] = $order_bill_to;
			$order_details['shipping'] = $order_shipping;
			$order_details['contact'] = $order_contact;
			$mediaClipOrderRequest['orderRequestHeader'] = $order_details;
			$mediaClipOrderRequest['itemOut'] = $order_item_details;
			$hubHelper = $om->create(mHelper::class); /** @var mHelper $hubHelper */
			$chekcoutMediaclipResponse =  $hubHelper->CheckoutWithSingleProduct($mediaClipOrderRequest);
			if ($chekcoutMediaclipResponse  && is_array($chekcoutMediaclipResponse)) {
				$mediaClipData['magento_order_id'] = $oid;
				$mediaClipData['mediaclip_order_id'] = $chekcoutMediaclipResponse['id'];
				$mediaClipData['mediaclip_order_details'] = json_encode($chekcoutMediaclipResponse);
				$resource = $om->get(ResourceConnection::class);
				$connection = $resource->getConnection();
				$tableName = $resource->getTableName('mediaclip_orders');
				$sql =
					"Insert Into " . $tableName . " (magento_order_id, mediaclip_order_id, mediaclip_order_details) 					Values (".$mediaClipData['magento_order_id'].",'".$mediaClipData['mediaclip_order_id']
					."','".$mediaClipData['mediaclip_order_details']."')"
				;
				$connection->query($sql);
			}
		}
    }
}