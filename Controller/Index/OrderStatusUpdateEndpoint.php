<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Inkifi\Mediaclip\Event;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IAttributeSetRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Model\StoreManagerInterface as IStoreManager;
use Mangoit\MediaclipHub\Model\Mediaclip;
use Mangoit\MediaclipHub\Model\Orders as mOrder;
use Mangoit\MediaclipHub\Model\Product as mP;
use pwinty\PhpPwinty;
use Zend\Log\Logger;
// 2018-11-02
class OrderStatusUpdateEndpoint extends Action {
    /**
	 * 2018-11-02
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
        IScopeConfig $scopeConfig,
        OI $orderitem,
        O $order,
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

	/**
	 * 2018-11-02
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
	 * @throws \Exception
	 */
    function execute() {
        $ev = $this->ev(); /** @var Event $ev */
        if ($s = $ev['status/value']) {  /** @var string|null $s */
			$this->l("Status: $s");
			$this->l($ev->j());
            if ('AvailableForDownload' === $s) {
				$this->pAvailableForDownload();
            }
            else if ('Shipped' === $s) {
				\Inkifi\Mediaclip\H\Shipped::p();
            }
        }
    }

	/**
	 * 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Generate JSON data for photo-books»: https://www.upwork.com/ab/f/contracts/21011549
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
	 * @used-by execute()
	 * @param string|null $v
	 * @param string $m
	 * @return string
	 */
    private function code($v, $m) {return $v ?: (
    	'gifting' === ($m = strtolower($m)) ? 'gift' : ('print' === $m ? 'prints-set-01' : 'photobook-jacket')
	);}

	/**
	 * 2019-02-24
	 * @used-by execute()
	 * @used-by l()
	 * @used-by pAvailableForDownload()
	 * @used-by pShipped()
	 * @return Event
	 */
	private function ev() {return Event::s();}

    /**
	 * 2018-11-02
     * @param int $product_id product id
     * @return String $mediaclip_module media clip produc type Photobook | Gifting | Print
     */
    private function getMediaclipModuleName( $product_id ) {
        $product = df_new_om(Product::class)->load($product_id);
        $attributeSet = df_new_om(IAttributeSetRepository::class);
        $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
        $mediaclip_module = $attributeSetRepository->getAttributeSetName();
        return $mediaclip_module;
    }

	/**
	 * 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Generate JSON data for photo-books»: https://www.upwork.com/ab/f/contracts/21011549
	 * @used-by execute()
	 * @param mixed $d
	 */
    private function l($d) {df_report(
    	"OrderStatusUpdate/{$this->ev()->oidI()}.log", is_string($d) ? $d : df_json_encode($d), true
	);}

	/**
	 * 2019-02-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * @used-by pAvailableForDownload()
	 * @param string $n
	 * @return Logger
	 */
	private function logger($n) {
		/** @var Logger $r */
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/$n.log");
        $r = new Logger;
        $r->addWriter($writer);
		return $r;
	}

	/**
	 * 2019-02-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * @used-by execute()
	 */
    private function pAvailableForDownload() {
    	$ev = $this->ev(); /** @var Event $ev */
    	$l = $this->logger('mediaclip_orders_download_shipment_status');
		$l->info($ev->oidE());
		$l->info($ev->j());
		$helper = mc_h();
		//Set mediaclip order status to 1 as the order is downloaded
		$mOrder = df_new_om(mOrder::class); /** @var mOrder $mOrder */
		$mOrderC = $mOrder->getCollection();
		$mOrderC->addFieldToFilter('magento_order_id', ['eq' => $ev->oidE()]);
		// 2018-08-17 Dmitry Fedyuk
		if ($mOrderData = df_first($mOrderC->getData())) {
			$this->l('mediaclipOrderData'); $this->l($mOrderData);
			$mOrder->setId($mOrderData['id']);
			$mOrder->setOrderDownloadStatus(1);
			$mOrder->save();
			$product_id = $ev['storeData/productId'];
			$product = df_new_om(Product::class)->load($product_id);
			$uploadfolder = $product->getMediaclipUploadFolder();
			$this->l("Upload folder: $uploadfolder");
			if ($uploadfolder == 'pwinty') {
				//set order item status to 1 as response of each line item receives
				$salesOrderItemModel = df_new_om(OI::class);
				$salesOrderItemModelCollection = $salesOrderItemModel->getCollection();
				$salesOrderItem = $salesOrderItemModelCollection
					->addFieldToFilter('mediaclip_project_id', ['eq' => $ev['projectId']]);
				foreach ($salesOrderItem as $key => $value) {
					$value->setItemDownloadStatus(1);
					$value->save();
				}
				$salesOrderItemModelCollection->clear()->getSelect()->reset('where');
				$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter(
					// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					// «Modify orders numeration for Mediaclip»
					// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
					'order_id', ['eq' => $ev->oidI()]
				);
				foreach ($salesOrderItem as $key => $value) {//get status of order item
					$status [] = $value->getData('item_download_status');
				}
				if (!in_array(0, $status)) { // check if all items are downloaded
					$merchantId = df_o(IScopeConfig::class)->getValue('api/pwinty_api_auth/merchant_id');
					$apiKey = df_o(IScopeConfig::class)->getValue('api/pwinty_api_auth/pwinty_api_key');
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
					$order = df_new_om(O::class)->load($ev->oidI());
					$orderIncrementId = $order['increment_id'];
					$entityId = $order->getEntityId();
					$orderDate = $order['created_at'];
					$mOrderDetails = $helper->getMediaClipOrders($entityId);
					foreach ($mOrderDetails->lines as $lines){
						$projectId = $lines->projectId;
						$projectData = df_new_om(Mediaclip::class)
							->load($projectId, 'project_id')->getData();
						$projectDetails[] = json_decode($projectData['project_details'], true);
					}
					$orderDirDate = $helper->createOrderDirectoryDate($orderDate);
					$imageArray = [];
					foreach ($projectDetails as $value) {
						$salesOrderItemModelCollection->clear()->getSelect()->reset('where');
						$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter(
							'mediaclip_project_id', array('eq' => $value['projectId'])
						);
						//get images from downloaded folder
						foreach ($salesOrderItem as $newvalue) {
							$orderItemID = $newvalue->getData('item_id');
						}
						$dir = df_o(DirectoryList::class);
						$base = $dir->getRoot();
						/** @var array(string => mixed) $mP */
						$mP = df_new_om(mP::class)->load($value['items'][0]['plu'], 'plu')->getData();
						$pwintyProduct = $mP['pwinty_product_name'];
						$frameColour = $mP['frame_colour'];
						$filesUploadPath =
							$base.'/mediaclip_orders/'.$orderDirDate.'/pwinty/'
							.$orderIncrementId.'/'.$orderItemID
							.'/'.$mP['product_label']
						;
						$imgPath = explode('html/', $filesUploadPath);
						$storeManager = df_o(IStoreManager::class);
						$store = $storeManager->getStore();
						$baseUrl = $store->getBaseUrl();
						if ($filesUploadPath != '') {
							$quantity = 0 ;
							foreach (new \DirectoryIterator($filesUploadPath) as $key => $fileInfo) {
								if ($fileInfo->isDot() || $fileInfo->isDir())
								   continue;
								if ($fileInfo->isFile() && $fileInfo->getExtension() != 'csv') {
									$img = $baseUrl.$imgPath[1].'/'.$fileInfo->getFilename();
									$imgAttribute = [];
									$imgAttribute['url'] = $img;
									$imgAttribute['sizing'] = "ShrinkToFit";
									$imgAttribute['priceToUser'] = "0";
									$imgAttribute['copies'] = $quantity+1;
									$imgAttribute['type'] = $pwintyProduct;
									foreach ($catalogue['items'] as  $value) {
										//check if product has frame attribute
										if ($value['name'] == $pwintyProduct) {
											if($frameColour != "" && !empty($value['attributes'])) {
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
					if ($address->getCompany() != ''){
						$street1 = $address->getCompany().','.$address->getStreet()[0];
					} else {
						$street1 = $address->getStreet()[0];
					}
					if (isset($address->getStreet()[1])) {
						$street2 = $address->getStreet()[1];
					} else{
						$street2 = '';
					}
					$city = $address->getCity();
					$customerId = $order->getCustomerId();
					$customer = df_new_om(Customer::class)->load($customerId);
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
					$mOrderModel = df_new_om(mOrder::class);
					$mOrderModelCollection = $mOrderModel->getCollection();
					$mOrder = $mOrderModelCollection
						->addFieldToFilter('magento_order_id', ['eq' => $ev->oidE()]);
					foreach ($mOrder as $key => $value) {
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
					} else {
						$logger->info('order is not submitted');
					}
				}
			}
			else {
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/json_status.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$salesOrderItemModel = df_new_om(OI::class);
				$salesOrderItemModelCollection = $salesOrderItemModel->getCollection();
				// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// «Modify orders numeration for Mediaclip»
				// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
				$order = df_new_om(O::class)->load($ev->oidI());
				$orderIncrementId = $order['increment_id'];
				$entityId = $order->getEntityId();
				$orderDate = $order['created_at'];
				$mOrderDetails = $helper->getMediaClipOrders($entityId);
				$orderDirDate = $helper->createOrderDirectoryDate($orderDate);
				$array = [];
				$this->l('mediaclipOrderDetails->lines count: ' . count($mOrderDetails->lines));
				foreach ($mOrderDetails->lines as $lines) {
					$this->l('A line:');  $this->l($lines);
					$projectId = $lines->projectId;
					$projectData = df_new_om(Mediaclip::class)->load($projectId, 'project_id')->getData();
					$projectDetails = json_decode($projectData['project_details'], true);
					$this->l('projectDetails:'); $this->l($projectDetails);
					$salesOrderItemModelCollection->clear()->getSelect()->reset('where');
					$salesOrderItem = $salesOrderItemModelCollection->addFieldToFilter(
						'mediaclip_project_id', array('eq' => $projectDetails['projectId'])
					);
					$module = '';
					$orderQuantity = 1;
					foreach ($salesOrderItem as $newvalue) {
						$orderItemID = $newvalue->getData('item_id');
						$orderQuantity = (int)$newvalue->getQtyOrdered();
						$module = $this->getMediaclipModuleName($newvalue->getData('product_id'));
					}
					$this->l("Module: $module");
					$dir = df_o(DirectoryList::class);
					$base = $dir->getRoot();
					/** @var array(string => mixed) $mP */
					$mP = df_new_om(mP::class)->load($projectDetails['items'][0]['plu'], 'plu')->getData();
					$this->l('Mediaclip Product:');  $this->l($mP);
					$ftp_json = $mP['ftp_json'];
					$logger->info($ftp_json);
					$this->l('Send Json: ' . $ftp_json);
					#@var $includeQuantityInJSON flag to include json
					$includeQuantityInJSON = $mP['include_quantity_in_json'];
					if ($ftp_json == 1) {
						$filesUploadPath =
							$base.'/mediaclip_orders/'.$orderDirDate.'/ascendia/'
							.$orderIncrementId.'/'.$orderItemID.'/'
							.$mP['product_label']
						;
						$this->l("filesUploadPath: $filesUploadPath");
						$logger->info(json_encode($filesUploadPath));
						$array['destination']['name'] = 'pureprint';
						$array['orderData']['sourceOrderId'] = $mOrderDetails->storeData->orderId;
						$linesDetails = $helper->getMediaClipOrderLinesDetails($lines->id);
						$this->l('linesDetails->files count: ' . count($linesDetails->files));
if (count($linesDetails->files)) {
$this->l('linesDetails->files:');  $this->l($linesDetails->files);
/**
* 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
* «Generate JSON data for photo-books»
* https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
* 2018-11-03
* An example of $linesDetails->files
*	[
*		{
*			"id": "photobook-jacket",
*			"productId": "$(package:inkifi/photobooks)/products/hard-cover-gray-210x210mm-70",
*			"plu": "INKIFI-HCB210-M-70",
*			"quantity": 1,
*			"url": "https://renderstouse.blob.core.windows.net/0c25168e-eda3-41d4-b266-8259566d2507/dust.pdf?sv=2018-03-28&sr=c&sig=XzCB%2B2CWlpqNFqVf6CnoVr8ICDGufTexaNqyzxMDUx8%3D&st=2018-11-02T19%3A36%3A41Z&se=2018-12-02T19%3A38%3A41Z&sp=r",
*			"order": 0
*		},
*		{
*			"id": "photobook-pages",
*			"productId": "$(package:inkifi/photobooks)/products/hard-cover-gray-210x210mm-70",
*			"plu": "INKIFI-HCB210-M-70",
*			"quantity": 1,
*			"url": "https://renderstouse.blob.core.windows.net/0c25168e-eda3-41d4-b266-8259566d2507/0d0e8542-db8d-475b-95bb-33156dc6551a_0c25168e-eda3-41d4-b266-8259566d2507.pdf?sv=2018-03-28&sr=c&sig=maMnPG2XIrQuLC3mArAgf3YKrM6EzFwNMggwApqMTeo%3D&st=2018-11-02T19%3A36%3A43Z&se=2018-12-02T19%3A38%3A43Z&sp=r",
*			"order": 1
*		}
*	]
*/
$array['orderData']['items'][] = [
'sku' => $mP['plu']
,'sourceItemId' => $lines->id
,'components' => array_values(df_map($linesDetails->files, function($f) use($module, $mP) {return [
	'code' => dfa($mP, 'json_code', $this->code(dfo($f, 'id'), $module)), 'fetch' => true, 'path' => $f->url
];}))
,'quantity' => 1 == $includeQuantityInJSON ? $orderQuantity : 1
];
}
					}
				}
				$this->l('array:'); $this->l($array);
				if (!empty($array)) {
					$logger->info(json_encode($array));
					$shippingMethod = $order->getShippingMethod();
					$address = $order->getShippingAddress();
					$postcode = $address->getPostcode();
					$countryCode = $address->getCountryId();
					$region = $address->getRegion();
					$telephone = $address->getTelephone();
					if ($address->getCompany() != ''){
						$street1 = $address->getCompany() . ',' . $address->getStreet()[0];
					}
					else {
						$street1 = $address->getStreet()[0];
					}
					if (isset($address->getStreet()[1])) {
						$street2 = $address->getStreet()[1];
					}
					else {
						$street2 = '';
					}
					$city = $address->getCity();
					$customerId = $order->getCustomerId();
					$customer = df_new_om(Customer::class)->load($customerId);
					$name = $address->getFirstname().' '.$address->getLastname();
					$email = $customer['email'];
					$array['shipments'] = [[
					   'shipTo' => [
							'name' => $name
							,'address1'=> $street1
							,'address2' => $street2
							,'town' => $city
							,'postcode' => $postcode
							,'isoCountry' => $countryCode
							,'state' => $region
							,'email' => $email
							,'phone' => $telephone
					   ],
					   'carrier' => ['alias' => $shippingMethod]
					]];
					// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					// "Replace the «/home/canvaspr/dev2.inkifi.com/html/ftp_json25june/»
					// hardcoded filesystem path with a dynamics one":
					// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/3
					$filesUploadPath = df_cc_path(
						BP, 'ftp_json', $orderDirDate, $orderIncrementId, $orderItemID, $mP['product_label']
					);
					$this->l("filesUploadPath: $filesUploadPath");
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
					$this->file->mkdir($filesUploadPath);
					$json_handler = fopen($jsonFile, 'w+');
					//here it will print the array pretty
					fwrite($json_handler, json_encode($array,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
					fclose($json_handler);
					$content = file_get_contents($jsonFile);
					$this->sftp->write($jsonRemoteFile, $content);
					$this->sftp->close();
				}
			}
		}
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

    private $_order;

    protected $_productloader;

    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
}