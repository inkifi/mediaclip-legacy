<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Result\PageFactory;
use Mangoit\MediaclipHub\Model\Orders;
use pwinty\PhpPwinty;
class OrderStatusUpdateEndpoint extends Action {
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    function __construct(
        Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\Io\Sftp $sftp,
        \Magento\Framework\Filesystem\Io\Ftp $ftp,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Item $orderitem,
        \Magento\Sales\Model\Order $order,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    ) {
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->sftp = $sftp;
        $this->ftp = $ftp;
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->_orderItem = $orderitem;
        $this->_order = $order;
        $this->_productloader = $_productloader;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    function execute() {
		/**
		 * 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * A response looks like:
		 * 	{
		 *		"id": "3ea5265e-46cf-42cd-97a8-1c292169e006",
		 *		"order": {
		 *			"storeData": {
		 *				"orderId": "40826"
		 *			}
		 *		},
		 *		"storeData": {
		 *			"lineNumber": 1,
		 *			"productId": "79772"
		 *		},
		 *		"projectId": "4a9a1d14-0807-42ab-9a03-e2d54d9b8d12",
		 *		"status": {
		 *			"value": "AvailableForDownload",
		 *			"effectiveDateUtc": "2018-08-15T22:51:43.5408397Z"
		 *		}
		 *	}
		 */
        $json = file_get_contents('php://input');
        $obj = json_decode($json, true);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mediaclip_orders_download_shipment_status.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if (!empty($obj) && isset($obj['status'])) {
        	$dfStatus = (string)$obj['status']['value']; /** @var string $dfStatus */
        	$oidE = $obj['order']['storeData']['orderId']; /** @var string $oidE */
            if ('AvailableForDownload' === $dfStatus) {
				// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// «Modify orders numeration for Mediaclip»
				// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
                $oidI = ikf_eti($oidE); /** @var string $oidI */
                $logger->info($oidE);
                $logger->info($json);
                if ($oidE) {
                    $helper = mc_h();
                    //Set mediaclip order status to 1 as the order is downloaded
                    $model = $this->_objectManager->create(Orders::class);
                    $mediaclipOrder = $model->getCollection();
                    $mediaclipOrderData = $mediaclipOrder->addFieldToFilter('magento_order_id', [
                    	'eq' => $oidE
					]);
					// 2018-08-17 Dmitry Fedyuk
					if ($mediaclipOrderData = df_first($mediaclipOrderData->getData())) {
						$model->setId($mediaclipOrderData['id']);
						$model->setOrderDownloadStatus(1);
						$model->save();
						$product_id = $obj['storeData']['productId'];
						$product = $this->_objectManager->create(Product::class)->load($product_id);
						$uploadfolder = $product->getMediaclipUploadFolder();
						if($uploadfolder == 'pwinty') {
							//set order item status to 1 as response of each line item receives
							$salesOrderItemModel = $this->_objectManager->create('Magento\Sales\Model\Order\Item');
							$salesOrderItemModelCollection = $salesOrderItemModel->getCollection();
							$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter('mediaclip_project_id', array('eq' => $obj['projectId']));

							foreach ($salesOrderItem as $key => $value) {

								$value->setItemDownloadStatus(1);
								$value->save();
							}

							$salesOrderItemModelCollection->clear()->getSelect()->reset('where');
							$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter(
							// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
							// «Modify orders numeration for Mediaclip»
							// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
								'order_id', ['eq' => $oidI]
							);

							foreach ($salesOrderItem as $key => $value) {//get status of order item

								$status [] = $value->getData('item_download_status');
							}

							if (!in_array(0, $status)) { // check if all items are downloaded

								$merchantId = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/pwinty_api_auth/merchant_id');
								$apiKey = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/pwinty_api_auth/pwinty_api_key');
								$config = array(  //log in to pwinty
									'api'        => 'sandbox',//production
									'merchantId' => $merchantId,
									'apiKey'     => $apiKey
								);
								$pwinty = new PhpPwinty($config);
								$catalogue = $pwinty->getCatalogue( //check pwinty product
									"GB",               //country code
									"Pro"               //quality
								);
								// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
								// «Modify orders numeration for Mediaclip»
								// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
								$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($oidI);
								$orderIncrementId = $order['increment_id'];
								$entityId = $order->getEntityId();
								$orderDate = $order['created_at'];
								$mediaclipOrderDetails = $helper->getMediaClipOrders($entityId);
								foreach ($mediaclipOrderDetails->lines as $lines){
									$projectId = $lines->projectId;
									$projectData = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($projectId, 'project_id')->getData();
									$projectDetails[] = json_decode($projectData['project_details'], true);
								}
								$orderDirDate = $helper->createOrderDirectoryDate($orderDate);
								$imageArray = [];

								foreach ($projectDetails as $value) {

									$quantity = $value['items'][0]['quantity'];
									$salesOrderItemModelCollection->clear()->getSelect()->reset('where');
									$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter('mediaclip_project_id', array('eq' => $value['projectId']));


									//get images from downloaded folder
									foreach ($salesOrderItem as $newvalue) {

									   $orderItemID = $newvalue->getData('item_id');
									}
									$dir = $this->_objectManager->get(DirectoryList::class);
									$base = $dir->getRoot();
									$mediaClipOrdersData = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Product')->load($value['items'][0]['plu'], 'plu')->getData();
									$pwintyProduct = $mediaClipOrdersData['pwinty_product_name'];
									$frameColour = $mediaClipOrdersData['frame_colour'];
									$filesUploadPath = $base.'/mediaclip_orders/'.$orderDirDate.'/pwinty/'.$orderIncrementId.'/'.$orderItemID.'/'.$mediaClipOrdersData['product_label'];
									$imgPath = explode('html/', $filesUploadPath);
									$storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
									$store = $storeManager->getStore();
									$baseUrl = $store->getBaseUrl();

									if ($filesUploadPath != '') {
										$quantity = 0 ;
										foreach (new \DirectoryIterator($filesUploadPath) as $key => $fileInfo) {

											if ($fileInfo->isDot() || $fileInfo->isDir())
											   continue;
											if ($fileInfo->isFile() && $fileInfo->getExtension() != 'csv') {

												$img = $baseUrl.$imgPath[1].'/'.$fileInfo->getFilename();
												$imgAttribute = array();
												$imgAttribute['url'] = $img;
												$imgAttribute['sizing'] = "ShrinkToFit";
												$imgAttribute['priceToUser'] = "0";
												$imgAttribute['copies'] = $quantity+1;
												$imgAttribute['type'] = $pwintyProduct;

												foreach ($catalogue['items'] as  $value) {//check if product has frame attribute

													if ($value['name'] == $pwintyProduct) {

														if($frameColour != "" && !empty($value['attributes'])){

															$imgAttribute['attributes'][$value['attributes'][0]['name']] = strtolower($frameColour);
														}
													}
												}
												$imageArray[$orderItemID] = $imgAttribute;
												$quantity++;
											}
										}
									}
								}
								$imageArray = array_values($imageArray);
								$address = $order->getShippingAddress();
								$postcode = $address->getPostcode();
								$countryCode = $address->getCountryId();
								$region = $address->getRegion();

								/*if($address->getCompany() != ''){

									$street1 = $address->getCompany().','.$address->getStreet(1);
								}else{

									$street1 = $address->getStreet(1);
								}*/
								if($address->getCompany() != ''){

									$street1 = $address->getCompany().','.$address->getStreet()[0];
								}else{

									$street1 = $address->getStreet()[0];
								}
								if (isset($address->getStreet()[1])) {

									$street2 = $address->getStreet()[1];
								}else{
									$street2 = '';
								}
								//$street2 = $address->getStreet(2);
								$city = $address->getCity();
								$customerId = $order->getCustomerId();
								$customer = $this->_objectManager->create('\Magento\Customer\Model\Customer')->load($customerId);
								$name = $customer['firstname'].' '.$customer['lastname'];
								$email = $customer['email'];

								$order = $pwinty->createOrder(// create order to pwinty
									$name,          //name
									$email,         //email address
									$street1,    //address1
									$street2,    //address 2
									$city,          //town
									$region,        //state
									$postcode,      //postcode or zip
									'GB',            //country code
									$countryCode,    //destination code
									true,            //tracked shipping
									"InvoiceMe",     //payment method
									"Pro"            //quality
								);

								$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pwinty_orders_status.log');
								$logger = new \Zend\Log\Logger();
								$logger->addWriter($writer);
								$logger->info($order);

								$pwintyOrderId = $order['id'];
								//save pwinty id to custom table
								$mediaclipOrderModel = $this->_objectManager->create(Orders::class);
								$mediaclipOrderModelCollection = $mediaclipOrderModel->getCollection();
								$mediaclipOrder = $mediaclipOrderModelCollection->addFieldToFilter('magento_order_id', array('eq' => $oidE));

								foreach ($mediaclipOrder as $key => $value) {

									$value->setPwintyOrderId($pwintyOrderId);
									$value->save();
								}
								$photos =  $pwinty->addPhotos( //add photos to order
									$pwintyOrderId, //order id
									$imageArray
								);

								$logger->info($photos);

								$getOrderStatus = $pwinty->getOrderStatus(// check order status
									$pwintyOrderId              //orderid
									 //status
								);

								$logger->info($getOrderStatus);

								if ($getOrderStatus['isValid'] == 1) {// submit order if no error

									$pwinty->updateOrderStatus(
										$pwintyOrderId,              //orderid
										"Submitted"         //status
									);
								}else{

									$logger->info('order is not submitted');
								}
							}
						}
						else {
							$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/json_status.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);

							$salesOrderItemModel = $this->_objectManager->create('Magento\Sales\Model\Order\Item');
							$salesOrderItemModelCollection = $salesOrderItemModel->getCollection();
							// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
							// «Modify orders numeration for Mediaclip»
							// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
							$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($oidI);


							$orderIncrementId = $order['increment_id'];
							$entityId = $order->getEntityId();
							$orderDate = $order['created_at'];
							$mediaclipOrderDetails = $helper->getMediaClipOrders($entityId);
							$orderDirDate = $helper->createOrderDirectoryDate($orderDate);
							$array = array();


							foreach ($mediaclipOrderDetails->lines as $lines){

								$projectId = $lines->projectId;
								$projectData = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($projectId, 'project_id')->getData();
								$projectDetails = json_decode($projectData['project_details'], true);


								$salesOrderItemModelCollection->clear()->getSelect()->reset('where');

								$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter('mediaclip_project_id', array('eq' => $projectDetails['projectId']));
								$quantity = $projectDetails['items'][0]['quantity'];


								$module = "";
								$orderQuantity = 1;

								foreach ($salesOrderItem as $newvalue) {
									$orderItemID = $newvalue->getData('item_id');
									$orderQuantity = (int)$newvalue->getQtyOrdered();
									$productSKU = $newvalue->getData('sku');
									$module = $this->getMediaclipModuleName($newvalue->getData('product_id'));
								}


								$dir = $this->_objectManager->get(DirectoryList::class);
								$base = $dir->getRoot();

								$mediaClipOrdersData = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Product')->load($projectDetails['items'][0]['plu'], 'plu')->getData();
								$ftp_json = $mediaClipOrdersData['ftp_json'];
								$logger->info($ftp_json);

								#@var $includeQuantityInJSON flag to include json
								$includeQuantityInJSON = $mediaClipOrdersData['include_quantity_in_json'];




								if ($ftp_json == 1) {


									// $filesUploadPath1 = $base.'/mediaclip_orders/'.$orderDirDate.'/ascendia/'.$orderIncrementId.'/'.$orderItemID.'/'.$mediaClipOrdersData['product_label'];

									$filesUploadPath = $base.'/mediaclip_orders/'.$orderDirDate.'/ascendia/'.$orderIncrementId.'/'.$orderItemID.'/'.$mediaClipOrdersData['product_label'];
									$logger->info(json_encode($filesUploadPath));
									$imgPath = explode('html/', $filesUploadPath);
									$storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
									$store = $storeManager->getStore();
									$baseUrl = $store->getBaseUrl();
									$array['destination']['name'] = 'pureprint';
									$array['orderData']['sourceOrderId'] = $mediaclipOrderDetails->storeData->orderId;
									$helper = mc_h();
									$linesDetails = $helper->getMediaClipOrderLinesDetails($lines->id);
// 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
// «Generate JSON data for photo-books»
// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
$defaultCode = function($m) {
	$m = strtolower($m);
	return 'gifting' === $m ? 'gift' : ('print' === $m ? 'prints-set-01' : 'photobook-jacket');
};
									foreach ($linesDetails->files as $key => $fileDetails) {
// 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
// «Generate JSON data for photo-books»
// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
$array['orderData']['items'][] = [
	'sku' => $mediaClipOrdersData['plu']
	,'sourceItemId' => $lines->id
	,'components' => [[
		'code' => $mediaClipOrdersData['json_code'] ?: $defaultCode($module)
		,'fetch' => true
		,'path' => $fileDetails->url
	]]
	,'quantity' => 1 == $includeQuantityInJSON ? $orderQuantity : 1
];
									}
								}
							}

							if (!empty($array)) {
								$dir = $this->_objectManager->get(DirectoryList::class);
								$base = $dir->getRoot();
								$logger->info(json_encode($array));
								$shippingMethod = $order->getShippingMethod();
								$address = $order->getShippingAddress();

								$postcode = $address->getPostcode();
								$countryCode = $address->getCountryId();
								$region = $address->getRegion();
								$telephone = $address->getTelephone();
								if($address->getCompany() != ''){

									$street1 = $address->getCompany().','.$address->getStreet()[0];
								}else{

									$street1 = $address->getStreet()[0];
								}
								if (isset($address->getStreet()[1])) {

									$street2 = $address->getStreet()[1];
								}else{
									$street2 = '';
								}
								$city = $address->getCity();
								$customerId = $order->getCustomerId();
								$customer = $this->_objectManager->create('\Magento\Customer\Model\Customer')->load($customerId);
								$name = $address->getFirstname().' '.$address->getLastname();

								$email = $customer['email'];
								$array['shipments'] = array(array(
							   'shipTo' => array('name' => $name, 'address1'=> $street1,'address2'=>$street2,'town'=>$city,'postcode'=>$postcode,'isoCountry'=>$countryCode,'state'=>$region,'email'=>$email,'phone'=>$telephone),
							   'carrier' => array('alias'=>$shippingMethod)
								));
								// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
								// "Replace the «/home/canvaspr/dev2.inkifi.com/html/ftp_json25june/»
								// hardcoded filesystem path with a dynamics one":
								// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/3
								$filesUploadPath = df_cc_path(
									BP, 'ftp_json', $orderDirDate, $orderIncrementId
									,$orderItemID, $mediaClipOrdersData['product_label']
								);
								$logger->info(json_encode($filesUploadPath));
								// 2018-08-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
								// «FTP upload to ftp.pureprint.com has stopped working»
								// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/6
								$this->sftp->open([
									'host' => 'ftp.pureprint.com'
									,'username' => 'Inkifi'
									,'password' => 'Summ3rD4ys!'
								]);
								/* Check SKU code here */
								$jsonFileName = $orderIncrementId.'.json';
								$jsonFile = $filesUploadPath.'/'.$jsonFileName;
								$jsonRemoteFile = '/Inkifi/'.$jsonFileName;
								if (!is_dir($filesUploadPath)) {
									$this->file->mkdir($filesUploadPath);
								} else {
									$this->file->mkdir($filesUploadPath);
								}
								$json_handler = fopen ($jsonFile, 'w+');
								fwrite($json_handler, json_encode($array,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));   //here it will print the array pretty
								fclose($json_handler);
								$content = file_get_contents($jsonFile);
								$this->sftp->write($jsonRemoteFile, $content);
								$this->sftp->close();
							}
						}
					}
                }
            }
            else if ('Shipped' === $dfStatus) {
                $projectId = $obj['projectId'];
				// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// «Modify orders numeration for Mediaclip»
				// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
                $oidI = ikf_eti($oidE); /** @var string $oidI */
                try {

					// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					// «Modify orders numeration for Mediaclip»
					// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($oidI);

                    $order_items = $order->getItemsCollection();

                    $item_qtys = array();
                    
                    foreach ($order_items as $item) {
                        if (($item->getQtyToShip() > 0) && (!$item->getIsVirtual())) {
                            $_productId = $item->getProductId();
                            $_product = $this->_objectManager->create(Product::class)->load($_productId);
                            $attributeSet = $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
                            $attributeSetRepository = $attributeSet->get($_product->getAttributeSetId());
                            $attribute_set_name = $attributeSetRepository->getAttributeSetName();
                
                            if ($attribute_set_name == 'Photobook') {
                                if ($item->getMediaclipProjectId() != '' && ($item->getMediaclipProjectId() == $projectId)) {
                                    $itemId = $item->getItemId();
                                    $item_qtys[$itemId] = $item->getQtyToShip();
                                }
                            }
                        }
                    }

                    if (!empty($item_qtys)) {
                        // Create Shipment
                        $shipment = $order->prepareShipment($item_qtys);
                        $shipment->register();
                        $shipment->sendEmail(true)
                                ->setEmailSent(true)
                                ->save();

                        $transactionSave = $this->_objectManager->create('\Magento\Framework\DB\Transaction')
                                           ->addObject($shipment)
                                           ->addObject($shipment->getOrder())
                                           ->save();
                 
                        // Update Magento Order State/Status to Processing/Sent To Picking
                        $order->setStatus('complete')
                              ->save();
                        // Success
                        $loggers = $oidE." Shipment created successfully ".json_decode($item_qtys);
                    } else {
                        $loggers = $oidE." No item found to make shipment.";
                    }
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mediaclip_orders_shipped_dispactched_status.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($loggers);
                    //Mage::log($logger, null, 'mediaclip_orders_shipped_dispactched_status.log');
                } catch (\Exception $e) {
                    // Log Error On Order Comment History
                    $order->addStatusHistoryComment('Failed to create shipment - '. $e->getMessage())
                              ->save();

                    // Error
                    $loggers = $oidE." Failed to create shipment";
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mediaclip_orders_shipped_dispactched_status.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($loggers);
                   
                    //Mage::log($logger, null, 'mediaclip_orders_shipped_dispactched_status.log');
                }
            }
        }
    }

    /**
     * @param int $product_id product id
     * @return String $mediaclip_module media clip produc type Photobook | Gifting | Print
     */
    private function getMediaclipModuleName( $product_id ) {

        $product = $this->_objectManager->create(Product::class)->load($product_id);
        $uploadfolder = $product->getMediaclipUploadFolder();
        $attributeSet = $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
        $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
        $mediaclip_module = $attributeSetRepository->getAttributeSetName();
        return $mediaclip_module;

    }

    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    protected $sftp;

    protected $ftp;

    protected $file;

    protected $scopeConfig;

    private $_orderItem;

    private $_dimensionField;

    private $_alterationField;

    private $_garment;

    private $_garmentStyle;

    private $_garmentStyleOption;

    private $_order;

    private $_warehouseFactory;

    protected $_productloader;

    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
}