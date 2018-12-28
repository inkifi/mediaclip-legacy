<?php
namespace Mangoit\MediaclipHub\Observer;

class Orderneo implements \Magento\Framework\Event\ObserverInterface
{   /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_replenish;
    protected $order;
    protected $logger;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    function __construct(
        \Mangoit\MediaclipHub\Model\OrdersFactory $replenish,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $order
    ) {
        $this->_objectManager = $objectManager;
        $this->_replenish = $replenish;
        $this->logger = $logger;
        $this->order = $order;
    }
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$orderId = $observer->getEvent()->getOrderIds();
        $order = $observer->getEvent()->getOrder();
        //$order = $this->order->load($orderId);
    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/new_observer_check.log');
        $loggerNew = new \Zend\Log\Logger();
        $loggerNew->addWriter($writer);
        $loggerNew->info('inObserver1');
        /*$loggerNew->info(json_encode($order->debug()));
        $supplierModel = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Supplier');
        $supplierDataCollection = $supplierModel->getCollection()->getData();
        $supplierData = array();
        if (!empty($supplierDataCollection)) {
            $loggerNew->info('!empty($supplierDataCollection)');
            foreach ($supplierDataCollection as $value) {
                $supplierData[$value['id']]['domain'] = $value['domain'];
                $supplierData[$value['id']]['value'] = $value['value'];
            }
        }
        $i = 1;
        $photobook = false;
        foreach ($order->getAllItems() as $_item) {

            if ($_item->getMediaclipProjectId()) {
                $loggerNew->info('if=>>$_item->getMediaclipProjectId()');
                $item_supplier_id = array();
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($_item->getProductId());
                //echo "<pre>"; print_r($product->debug());
                $product_id = $_item->getProductId();
                $product_desc = $product->getShortDescription();

                $item_id['buyerPartId'] = $product_id;   //{YOUR-STORE-PRODUCT-ID}
                $item_id['supplierPartAuxiliaryId'] = $_item->getMediaclipProjectId();   //{HUB-PROJECT-ID}

                if ($product->getMediaclipProductSupplier()) {
                    $loggerNew->info('if=>>$product->getMediaclipProductSupplier()');
                    if (!empty($supplierData)) {
                        $loggerNew->info('if=>>!empty($supplierData)');
                        $supplierId = $product->getMediaclipProductSupplier();
                        if (isset($supplierData[$supplierId])) {
                            $loggerNew->info('if=>>isset($supplierData[$supplierId])');
                            $item_supplier_id['domain'] = $supplierData[$supplierId]['domain'];
                            $item_supplier_id['value'] = $supplierData[$supplierId]['value'];
                            $item_detail[$item_supplier_id['domain']] = $item_supplier_id['value'];
                        }
                    }
                }

                $item_detail['unitPrice']['money']['currency'] = $order->getOrderCurrencyCode();
                $item_detail['unitPrice']['money']['value'] = $_item->getPrice();
                
                $item_detail['description'] = $product_desc;

                $item['lineNumber'] = $i;
                $item['itemId'] = $item_id;
                $attributeSet = $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
                $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
                $attribute_set_name = $attributeSetRepository->getAttributeSetName();
                if ($attribute_set_name == 'Photobook') {

                    $photobook = true;
                    //$item['supplierId'] = $item_supplier_id;
                    $item_quantity = (int)$_item->getQtyOrdered();
                }

                if ($attribute_set_name == 'Print') {
                    $item_quantity = 1;
                }

                if ($attribute_set_name == 'Gifting') {
                    $item_quantity = (int)$_item->getQtyOrdered();
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
        $loggerNew->info('before=>>!empty($order_item_details)');
        if (!empty($order_item_details)) {
            $loggerNew->info('if=>>!empty($order_item_details)');
            $order_id = $order->getId();
            $order_date = $order->getCreatedAt();
           
            $shipping_address = $order->getShippingAddress()->getData();
            $order_ship_to = array();
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
                $country = $this->_objectManager->create('\Magento\Directory\Model\Country')->loadByCode($countryCode);
                $countryName = $country->getName();
                
                $postalAddress['country']['isoCountryCode'] = $countryCode;
                $postalAddress['country']['value'] = $countryName;

                $email['value'] = $shipping_address['email'];
                $phone['number'] = $shipping_address['telephone'];

                $order_ship_to['address']['postalAddress'] = $postalAddress;
                $order_ship_to['address']['email'] = $email;
                $order_ship_to['address']['phone'] = $phone;
            }
          //echo "<pre>";  print_r($item_supplier_id);
           // print_r($order_item_details);
            if (!empty($item_supplier_id)) {
                //print_r($order_item_details);
                if ($order_item_details[0]['supplierId']['value'] == 'OneFlowCloud') {
                    $carrierIdentifier_carrier['domain'] = 'alias';
                    $carrierIdentifier_carrier['value'] = $order->getShippingMethod();

                    $carrierIdentifier[] = $carrierIdentifier_carrier;

                    $order_ship_to['carrierIdentifier'] = $carrierIdentifier;
                }
            }
            $billing_address = $order->getBillingAddress()->getData();
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
                $country = $this->_objectManager->create('\Magento\Directory\Model\Country')->loadByCode($countryCode);
                $countryName = $country->getName();
                
                $postalAddress['country']['isoCountryCode'] = $countryCode;
                $postalAddress['country']['value'] = $countryName;

                $email['value'] = $billing_address['email'];
                $phone['number'] = $billing_address['telephone'];

                $order_bill_to['address']['postalAddress'] = $postalAddress;
                $order_bill_to['address']['email'] = $email;
                $order_bill_to['address']['phone'] = $phone;
            }

            $order_shipping['money']['currency'] = $order->getOrderCurrencyCode();
            $order_shipping['money']['value'] = $order->getShippingAmount();
            $order_shipping['description']['value'] = $order->getShippingDescription();

            if($order->getCustomerId() === NULL){
                $customer_id = $order->getCustomerId();
            } else {
                $session = $this->_objectManager->create('Magento\Catalog\Model\Session');
                if ($session->getMediaClipUserId()) {
                    $customer_id = $session->getMediaClipUserId();
                } else {
                    $customer_id = $order->getCustomerName();
                }
            }

            $contact_details['idReference']['identifier'] = $customer_id;
            $contact_details['idReference']['domain'] = 'storeUserId';

            $order_contact = $contact_details;

            $order_details['orderID'] = $order_id;
            $order_details['orderDate'] = $order_date;
            $order_details['shipTo'] = $order_ship_to;
            $order_details['billTo'] = $order_bill_to;
            $order_details['shipping'] = $order_shipping;
            $order_details['contact'] = $order_contact;

            $mediaClipOrderRequest['orderRequestHeader'] = $order_details;
            $mediaClipOrderRequest['itemOut'] = $order_item_details;

            $hubHelper = $this->_objectManager->create('Mangoit\MediaclipHub\Helper\Data');
           //echo "<pre>"; print_r($mediaClipOrderRequest);
            
            $loggerNew->info(json_encode($mediaClipOrderRequest));
            $chekcoutMediaclipResponse =  $hubHelper->CheckoutWithSingleProduct($mediaClipOrderRequest);
            
            $loggerNew->info(json_encode($chekcoutMediaclipResponse));
            if ($chekcoutMediaclipResponse  && is_array($chekcoutMediaclipResponse)) {
                
                $mediaClipData['magento_order_id'] = $order_id;
                $mediaClipData['mediaclip_order_id'] = $chekcoutMediaclipResponse['id'];
                $mediaClipData['mediaclip_order_details'] = json_encode($chekcoutMediaclipResponse);

                try{
                    
                    $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                    $connection = $resource->getConnection();
                    $tableName = $resource->getTableName('mediaclip_orders');
                    $sql = "Insert Into " . $tableName . " (magento_order_id, mediaclip_order_id, mediaclip_order_details) Values (".$mediaClipData['magento_order_id'].",'".$mediaClipData['mediaclip_order_id']."','".$mediaClipData['mediaclip_order_details']."')";
                    
                    $connection->query($sql);
                    
                    $loggerNew->info(json_encode($sql));
                    //$hubHelper->saveMediaclipOrder($mediaClipData);
                }catch(Exception $e){
                    echo $e->getMessage(); die;
                }
            }
        }*/
    }
}