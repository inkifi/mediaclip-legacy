<?php
namespace Mangoit\MediaclipHub\Helper;
use Inkifi\Mediaclip\Settings as S;
use Magento\Store\Model\Store;
use Mangoit\MediaclipHub\Model\Product as mProduct;
use Zend\Log\Logger as zL;
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 */
	protected $HUBURL;
	protected $STOREAPPID;
	protected $STORESECRET;
	protected $supplierFolderPath;
	/**
	 * @var \Magento\Framework\App\Response\Http
	 */
	protected $response;

	function __construct(\Magento\Framework\App\Helper\Context $context,\Magento\Framework\UrlInterface $response
	) {
		$this->response = $response;
		parent::__construct($context);
	}

	function getModules()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection();
		$options = $model->getData();
		$arr = array();
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		if ($options) {

			foreach ($options as $option) {

				$arr['value'] = $option['id'];
				$arr['label'] = $option['module_name'];
				$finalOptions[] = $arr;
			}
		}
		return $finalOptions;
	}
	function getThemes()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('Mangoit\MediaclipHub\Model\Theme')->getCollection();
		$options = $model->getData();
		$arr = array();
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		if ($options) {

			foreach ($options as $option) {

				$arr['value'] = $option['theme_url'];
				$arr['label'] = $option['label'];
				$finalOptions[] = $arr;
			}
		}
		return $finalOptions;
	}
	function getSuppliers()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('Mangoit\MediaclipHub\Model\Supplier')->getCollection();
		$options = $model->getData();
		$arr = array();
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		if ($options) {

			foreach ($options as $option) {

				$arr['value'] = $option['id'];
				$arr['label'] = $option['title'];
				$finalOptions[] = $arr;
			}
		}
		return $finalOptions;
	}
	function getDustjacketpopup()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$arr = array();
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		for ($i= 0; $i <= 5 ; $i++) {

			$conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('configuration/configurationpopup/dust_jacket_'.$i);
			//print_r($conf);
			if ($conf) {

				$arr['value'] = 'dust_jacket_'.$i;
				$arr['label'] = "Dust Jacket Product Popup"." ".$i;
				$finalOptions[] = $arr;
			}
		}
		return $finalOptions;
	}
	function getuploadFolder()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$arr = array();
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		$conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('ftp/ftp_upload/upload_folder');
		$folder_name = array();
		$folders_options = array(array('value' => '','label' => 'Please Select'));
		if ($conf != ''){
			$folder_names = explode(',', $conf);
			foreach($folder_names as $name) {
				$options = array();
				$options['label'] = $name;
				$options['value'] = $name;
				$folders_options[] = $options;
			}
		}
		return $folders_options;
	}
	function getPhotobookProduct()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$modulesModel = $objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection();
		$modelId = $modulesModel->addFieldToFilter('module_code', array('eq' => 'photobook'));
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		$moduleid = '';
		if ($modelId) {

			foreach ($modelId->getData() as  $value) {
				$moduleid = $value['id'];
			}
			if ($moduleid != '') {

				$model = $objectManager->create('Mangoit\MediaclipHub\Model\Product')->getCollection();
				$photobookProduct = $model->addFieldToFilter('module', array('eq' => $moduleid));
				$options = $photobookProduct->getData();
				$arr = array();
				if ($options) {
					foreach ($options as $option) {
						$arr['value'] = $option[mProduct::F__PLU];
						$arr['label'] = $option['product_label'];
						$finalOptions[] = $arr;
					}
				}
			}
		}
		return $finalOptions;
	}
	function getGiftingProduct()
	{
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		$moduleid = '';
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$modulesModel = $objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection();
		$modelId = $modulesModel->addFieldToFilter('module_code', array('eq' => 'gifting'));
		if ($modelId) {

			foreach ($modelId->getData() as  $value) {
				$moduleid = $value['id'];
			}
			if ($moduleid != '') {

				$model = $objectManager->create('Mangoit\MediaclipHub\Model\Product')->getCollection();
				$giftingProduct = $model->addFieldToFilter('module', array('eq' => $moduleid));
				$options = $giftingProduct->getData();
				$arr = array();
				if ($options) {

					foreach ($options as $option) {

						$arr['value'] = $option[mProduct::F__PLU];
						$arr['label'] = $option['product_label'];
						$finalOptions[] = $arr;
					}
				}
			}
		}

		return $finalOptions;
	}
	function getPrintProduct()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$modulesModel = $objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection();
		$finalOptions = array(array('value' => '','label' => 'Please Select'));
		$moduleid = '';
		$modelId = $modulesModel->addFieldToFilter('module_code', array('eq' => 'print'));
		if ($modelId) {

			foreach ($modelId->getData() as  $value) {
				$moduleid = $value['id'];
			}
			if ($moduleid != '') {

				$model = $objectManager->create('Mangoit\MediaclipHub\Model\Product')->getCollection();
				$printProduct = $model->addFieldToFilter('module', array('eq' => $moduleid));
				$options = $printProduct->getData();
				$arr = array();
				if ($options) {

					foreach ($options as $option) {

						$arr['value'] = $option[mProduct::F__PLU];
						$arr['label'] = $option['product_label'];
						$finalOptions[] = $arr;
					}
				}
			}
		}
		return $finalOptions;
	}

	function getEditorLinkUrl($product, $additional = array())
	{

		return $this->getEditorUrl($product, $additional);
	}

	function getEditorUrl($product, $additional = array())
	{

		$routeParams = array(
			'product' => $product->getEntityId()
		);


		if (!empty($additional)) {
			if (isset($additional['options'])) {
				$additional['options'] = json_encode($additional['options']);
			}
			$routeParams = array_merge($routeParams, $additional);
		}

		return $this->response->getUrl('mediacliphub/product/edit', $routeParams);
	}


	function getGUID(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = //chr(123)// "{"
				substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12);
				//.chr(125);// "}"
			return strtolower($uuid);
		}
	}

	/**
	 * Get customer_id
	 */
	function getCustomerId()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$core_session = $objectManager->get('Magento\Customer\Model\Session');
		$customer_session = $objectManager->get('Magento\Customer\Model\Session');

		if($customer_session->isLoggedIn()){
			//die('if');
			$mediaclip_customer_id = $customer_session->getId();
			$mediaclip_user_type = 'registered';
		}else{
			//die('else');
			$mediaclip_customer_id = $core_session->getMediaClipUserId();
			$mediaclip_user_type = 'anonymous';

			if (!$mediaclip_customer_id || $mediaclip_customer_id == '') {
				$mediaclip_customer_id = $this->getGUID();
			}
		}
		//die('out');
		$core_session->setMediaClipUserId($mediaclip_customer_id);
		$core_session->setMediaClipUserType($mediaclip_user_type);

		return $mediaclip_customer_id;
	}

	function CreateProject(
		$module, $productId, $theme, $storeProductId,
		$userToken, $additionalProprties = [], $mediaclipProductDateOptions
	) {
		$requestBody = array(
			"designerData" => array(
				"module" => $module,
				"productId" => $productId,
				"themeUrl" => $theme
			),
			"properties" => array(
				"storeProductId" => $storeProductId
			)
		);

			if ($mediaclipProductDateOptions && !empty($mediaclipProductDateOptions)) {

				$requestBody['designerData']['options'] = $mediaclipProductDateOptions;

			}


		if (!empty($additionalProprties)) {
			$requestBody['properties'] = array_merge($requestBody['properties'], $additionalProprties);
		}
		$this->HUBURL = S::s()->url();
		$curl = $this->BuildCurl("POST", $this->HUBURL . "/projects", $this->GetEndUserAuthorizationHeader($userToken),  $requestBody);

		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpCode != 201)
		{

			$this->ThrowHttpException("Could not create Hub project", $httpCode, $response);
		}

		$projectResponse = json_decode($response);
		return $projectResponse->id;
	}

	/**
	 * 2018-09-11
	 * @used-by RenewToken()
	 * @param $method
	 * @param $url
	 * @param $authorization
	 * @param object|array|false $data [optional]
	 * @return resource
	 */
	private function BuildCurl($method, $url, $authorization, $data = false)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$data_string = json_encode($data);

		switch ($method)
		{
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);
				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Authorization: ' . $authorization,
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string)
		));

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		return $curl;
	}
	private  function GetEndUserAuthorizationHeader($userToken)
	{

		$authorizationHeader = 'HubStoreUserToken ' . $userToken;
		return $authorizationHeader;
	}
	public  function HandleUserToken($storeUserId, $token)
	{

		$initialTimeZone = date_default_timezone_get();
		date_default_timezone_set("UTC");

		/**
		 * 2019-03-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * The «Trying to get property of non-object» error here means that
		 * the Mediaclip API KEY is incorrect.
		 * Mediaclip responds «Request must have a valid AppId authentication key and secret»
		 * and returns an empty array as a token, so the $token->expirationUtc expression fails.
		 * https://www.upwork.com/messages/rooms/room_9b50f413f4e119199fc9ccdf574a7e18/story_f0e5d6545846fbe4de29171e3a58f26c
		 * https://www.upwork.com/messages/rooms/room_9b50f413f4e119199fc9ccdf574a7e18/story_503606a3e5eb8a0060d55b133773cf29
		 */
		if (is_null($token) || (strtotime($token->expirationUtc) - 60) < time()) {

			$token = $this->GetTokenForEndUser($storeUserId);

		}

		date_default_timezone_set($initialTimeZone);

		return $token;
	}

	//Get a token for the end user. With the token, end user can create and edit project ton hub.
	public  function GetTokenForEndUser($storeUserId)
	{
		//The store user id is required to get a new token.
		$postRequestBody['storeData'] = array("userId" => $storeUserId);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$core_session =   $objectManager->get('Magento\Customer\Model\Session');
		//$core_session = Mage::getSingleton('core/session');
		if($core_session->getMediaClipUserType() == 'anonymous'){
			$postRequestBody['roles'] = array("Anonymous");
		}
		$this->HUBURL = S::s()->url();
		$checkoutLogger = ikf_logger('checkout'); /** @var zL $checkoutLogger */
		$endPoint = $this->HUBURL."/auth/jwt";
		$checkoutLogger->info(
						json_encode(
							array(
							"=============GET Token for End user: Start Request====================="
							), JSON_PRETTY_PRINT
						)
					);
		$checkoutLogger->info(
						json_encode(
							array(
								"User Type " =>$core_session->getMediaClipUserType(),
								"Method"=>"POST",
								"End Point" => $endPoint,
								"Authorization Header" => $this->GetStoreAuthorizationHeader(),
								"Post Body "=> $postRequestBody
							), JSON_PRETTY_PRINT
						)
					);


		$curl = $this->BuildCurl(
								"POST",
								$endPoint,
								$this->GetStoreAuthorizationHeader(),
								$postRequestBody
							);


		$response = curl_exec($curl);


		$checkoutLogger->info(
						json_encode(
							array(
							"=============GET Token for End user : End Request ======================"
							),JSON_PRETTY_PRINT
						)
					);
		 $checkoutLogger->info(
						json_encode(
							array(
							"Response : " => $response
							),JSON_PRETTY_PRINT
						)
					);

		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpCode != 201)
		{
			return array();
			//self::ThrowHttpException("Could not create Hub user token", $httpCode, $response);
		}

		$userTokenInfo = json_decode($response);

		return $userTokenInfo;
	}

	/**
	 * 2019-01-29
	 * @used-by CheckoutWithSingleProduct()
	 * @used-by consolidateCustomerAndGetCustomerToken()
	 * @used-by getMediaClipProjects()
	 * @used-by GetTokenForEndUser()
	 * @used-by renewMediaClipToken()
	 * @used-by RenewToken()
	 * @param Store $store [optional]
	 * @return string
	 */
	private function GetStoreAuthorizationHeader(Store $store = null) {
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
		$s = S::s($store);
		$this->STOREAPPID = $s->id();
		$this->STORESECRET = $s->key();
		$authorizationHeader = 'HubApi ' . base64_encode($this->STOREAPPID . ":" . $this->STORESECRET);
		return $authorizationHeader;
	}

	function checkUserToken($postData){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$session =   $objectManager->get('Magento\Customer\Model\Session');
		//$session = Mage::getSingleton('core/session');
		$model = $objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($postData['projectId'], 'project_id');


		if ($model) {

			$currentUserId = $this->getCustomerId();
			$projectUserId = $model->getUserId();
			/*print_r('$currentUserId=>');
			print_r($currentUserId);
			print_r('$projectUserId=>');
			print_r($projectUserId);die();*/
			// 2018-11-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «Improve Mediaclip integration with Magento 2: fix the frontend reordering scenario»
			// https://www.upwork.com/ab/f/contracts/21123596
			// I have just added the `true ||` condition.
			if (true || $currentUserId == $projectUserId) {
				$tokenObj = $session->getMediaClipToken();
				if ($tokenObj) {
					$initialTimeZone = date_default_timezone_get();
					date_default_timezone_set("UTC");
					if (is_null($tokenObj) || (strtotime($tokenObj->expirationUtc) - 60) < time()) {
						$token = array('token' => $tokenObj->token);
						$newtoken = $this->renewMediaClipToken($token);
						$session->setMediaClipToken($newtoken);
					}
					date_default_timezone_set($initialTimeZone);
				} else {

					$newtoken = $this->HandleUserToken($currentUserId, $tokenObj);
					$session->setMediaClipToken($newtoken);
				}
				return true;
			}
			else{

				return false;
			}
		}
	}

	function renewMediaClipToken($userToken) {
		$this->HUBURL = S::s()->url();
		$curl = $this->BuildCurl("POST", $this->HUBURL . "/auth/jwt/renew", $this->GetStoreAuthorizationHeader(), $userToken);
		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($httpCode != 200)
		{
			$this->ThrowHttpException("Could not renew Hub user token", $httpCode, $response);
		}
		$userTokenInfo = json_decode($response);
		return $userTokenInfo;
	}


	public static function IncludeJavascriptToStartDesigner($projectId, $editorContainerId, $mode, $userToken)
	{
		//print_r($userToken); die();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$store = $storeManager->getStore();
		$editProjectMode = empty($mode) ? "newProject" : htmlspecialchars($mode);
		?>
		<script type="text/javascript">

			//Settings required to run the Mediaclip Designer
			//var mediacliptoken = "<?php //echo $userToken ?>";
			launcherSettings = {
				storeUserToken: "<?php echo $userToken ?>", //Temporary end user token.
				projectId: "<?php echo htmlspecialchars($projectId)?>",
				container: document.getElementById('<?php echo $editorContainerId ?>'),
				keepAliveUrl: "<?php echo $store->getBaseUrl().'mediacliphub/index/renewMediaclipToken' ?>", //Server call to extend the current temporary user token
				mode: "<?php echo $editProjectMode ?>", //'mode' can be 'newProject' or 'editCartProject'
				flags: 'preview-instagram',
				onError: onErrorHandler,
				onSaveComplete: onSaveCompleted
			};

			window.mediaclip.hub.launch(launcherSettings);
			//window.mediaclip.hub.updateStoreUserToken(mediacliptoken);

			function onErrorHandler(error, details) {
				if (console && console.error) {
					console.error(error, details);
				}
			}

			function onSaveCompleted() {
				customerLoginRegister();
			}

		</script>
		<?php
	}

	/**                                        
	 * 2018-09-11
	 * The end user token has a short life span. A token can be extended.
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
	 * @param object $req
	 * @return array|mixed
	 */
	final function RenewToken($req) {
		$this->HUBURL = S::s()->url();
		$curl = $this->BuildCurl(
			'POST', "{$this->HUBURL}/auth/jwt/renew", $this->GetStoreAuthorizationHeader(), $req
		);
		$res = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($httpCode != 200) {
			df_error("/auth/jwt/renew failed with the code $httpCode");
		}
		return json_decode($res);
	}
	
	function getMediaClipUserToken(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$session = $objectManager->get('Magento\Customer\Model\Session');
		//$session = Mage::getSingleton('core/session');
		$tokenObj = $session->getMediaClipToken();
		if ($tokenObj) {
			$token = $tokenObj->token;
		} else {
			$storeUserId = $this->getCustomerId();

			$tokenObj = $this->HandleUserToken($storeUserId, $tokenObj);
			$session->setMediaClipToken($tokenObj);
			$token = $tokenObj->token;
		}
		return $token;
	}

	/**
	 * 2019-01-29
	 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
	 * @param array(string => mixed) $postRequestBody
	 * @param Store $store
	 * @return mixed
	 */
	function CheckoutWithSingleProduct(array $postRequestBody, Store $store)
	{
	   // print_r(json_encode($postRequestBody)); die();
		$lCheckout = ikf_logger('checkout'); /** @var zL $lCheckout */
		$lHelper = ikf_logger('helper'); /** @var zL $lHelper */
		$lHelper->info(json_encode($postRequestBody));
		$lCheckout->info(json_encode($postRequestBody));
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
		$s = S::s($store);  /** @var S $s */
		$this->HUBURL = $s->url();
		$this->STOREAPPID = $s->id();
		$curl = $this->BuildCurl('POST'
			,"$this->HUBURL/stores/$this->STOREAPPID/orders"
			,$this->GetStoreAuthorizationHeader($store)
			,$postRequestBody
		);
		$response = curl_exec($curl);
		$lCheckout->info(json_encode($response));
		if($response) {
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			sleep(0.3);
			try {
					if ($response && $httpCode == 201){
						$checkoutInformation = json_decode($response, true);

						$lHelper->info($response);
						return $checkoutInformation;
					}
					$lHelper->info($response);
				} catch (\Exception $e) {
					$lCheckout->info(
						json_encode(
							array(
							"Checkout error  " => $e->getMessage()
							),JSON_PRETTY_PRINT
						)
					);
					//$this->messageManager->addError($e->getMessage());
					echo $e->getMessage();
				}
		}
		return null;
	}

	function consolidateCustomerAndGetCustomerToken($storeUserId, $anonymousCustomerId) {
		$l = ikf_logger('checkout_login'); /** @var zL $l */
		$postRequestBody['storeData'] = array("anonymousUserId" => $anonymousCustomerId);
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
		$this->HUBURL = S::s()->url();
		$endPoint = $this->HUBURL."/stores/".S::s()->id()."/users/".$storeUserId."/consolidation";
		$curl = $this->BuildCurl("POST", $endPoint, $this->GetStoreAuthorizationHeader(), $postRequestBody);
		$response = curl_exec($curl);
		/* Consolidate customer response */
		$l->info( "====Request for login ====" );
		$l->info(
					json_encode(
						array(
							"Request " =>array(
								"End Point"=> $endPoint,
								"Header" => $this->GetStoreAuthorizationHeader(),
								"Request Data"=>$postRequestBody
							)

						),
						JSON_PRETTY_PRINT
					)
				);

		$l->info( "====Response for login ====" );
		$l->info($response);


		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		/*if ($httpCode != 201)
		{
			self::ThrowHttpException("Could not create Hub user token", $httpCode, $response);
		}*/


		$userTokenInfo = json_decode($response);

		return $userTokenInfo;
	}
	function getMediaClipProjects($storeUserId) {
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
		$service_url = S::s()->url().'/stores/'.S::s()->id().'/users/'.$storeUserId.'/projects/';
		$curl = curl_init($service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$authorization = $this->GetStoreAuthorizationHeader();
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Authorization: ' . $authorization,
		));

		$curl_response = curl_exec($curl);

		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}

		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($httpCode != 200)
		{
			return array();
			//self::ThrowHttpException("Error in the request made: ", $httpCode, $response);
		}

		$decoded = json_decode($curl_response);

		return $decoded;
	}

	function saveMediaclipOrder($postData){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$data_string = json_encode($postData);
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$store = $storeManager->getStore();
		$url = $store->getBaseUrl()."mediacliphub/index/requestmediaclipordersave";
		$curl = $objectManager->create('\Magento\Framework\HTTP\Client\Curl');
		$curl->post($url, $postData);
		//$model = Mage::getModel('mediaclub/mediacluborders');
		echo "string123";
		//$model->setData($postData)->save();
		die('644');
	}

	function createOrderDirectory($orderId, $order_created_at, $supplierDirName , $orderItemID){
		$order_day = date('D', strtotime($order_created_at));
		$present = strtotime($order_created_at);
		$folder_name = date('d-m-Y', $present);
		/*$future = strtotime(date("d-m-Y")." 08:59:59");
		switch ($order_day) {
			case 'Mon':
				$past = strtotime(date('d-m-Y', strtotime('last Fri'))." 09:00:00");
				if ($present >= $past && $present <= $future) {
					$folder_name = date('d-m-Y', $present);
				} else if ($present > $future) {
					$folder_name = date('d-m-Y', strtotime('next Wed', $present));
				}
				break;

			case 'Tue':
				$folder_name = date('d-m-Y', strtotime('next Wed', $present));
				break;

			case 'Wed':
				$past = strtotime(date('d-m-Y', strtotime('last Mon'))." 09:00:00");
				if ($present >= $past && $present <= $future) {
					$folder_name = date('d-m-Y', $present);
				} else if ($present > $future) {
					$folder_name = date('d-m-Y', strtotime('next Fri', $present));
				}
				break;

			case 'Thu':
				$folder_name = date('d-m-Y', strtotime('next Fri', $present));
				break;

			case 'Fri':
				$past = strtotime(date('d-m-Y', strtotime('last Wed'))." 09:00:00");
				if ($present >= $past && $present <= $future) {
					$folder_name = date('d-m-Y', $present);
				} else if ($present > $future) {
					$folder_name = date('d-m-Y', strtotime('next Mon', $present));
				}
				break;

			case 'Sat':
				$folder_name = date('d-m-Y', strtotime('next Mon', $present));
				break;

			case 'Sun':
				$folder_name = date('d-m-Y', strtotime('next Mon', $present));
				break;

			default:
				break;
		}*/

		if ($folder_name) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

			$directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

			$rootPath  =  $directory->getRoot();

			$rootFolderPath = $rootPath."/mediaclip_orders";

			$this->createDirectory($rootFolderPath);

			$orderDateFolderPath = $rootFolderPath.'/'.$folder_name;

			$this->createDirectory($orderDateFolderPath);

			$orderDateFolderPathSupplier = $orderDateFolderPath;

			if ($supplierDirName) {
				$supplierDirName = preg_replace('/\s+/', '-', trim($supplierDirName));
				$orderDateFolderPathSupplier =  $orderDateFolderPath.'/'.$supplierDirName;
				$this->createDirectory($orderDateFolderPathSupplier);
			}

			$this->supplierFolderPath = $orderDateFolderPathSupplier;

			$orderFolderPath = $orderDateFolderPathSupplier.'/'.$orderId;
			$this->createDirectory($orderFolderPath);
			$orderFolderPathTemp = $orderFolderPath.'/'.$orderItemID;

			if ($this->checkDir($orderFolderPathTemp , $supplierDirName)) {
				$orderFolderPath = $orderFolderPathTemp;
				$this->createDirectory($orderFolderPath);
			}
			return $orderFolderPath;
		}
		return false;
	}
	function checkDir($dirPath , $supplierDirName ='' ){
		$response = true;
		//if ($supplierDirName != 'pwinty') {

			if(is_dir($dirPath)){
				if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
						$dirPath .= '/';
				}
				$files = glob($dirPath . '*', GLOB_MARK);
				foreach ($files as $file) {
					if (is_dir($file)) {
						self::checkDir($file);
					} else {
						unlink($file);
					}
				}

				rmdir($dirPath);
			}
		//}
		return $response;
	}

	function createDirectory($path){
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
	}
	function uploadEmailsCSV($supplierFolderPath, $orderIncrementId, $customerEmail){

		$emailCsvPath = $supplierFolderPath;
		$emailCsvName = 'emails.csv';
		$emailCsvFile = $emailCsvPath.'/'.$emailCsvName;
		if (file_exists($emailCsvFile)) {
			$file = fopen($emailCsvFile,"r");

			$i = 0;
			$flag = true;
			$emailCsvContentArray = array();
			while(! feof($file)) {
				$emailCsvContent = fgetcsv($file);
				$emailCsvContentArray[] = $emailCsvContent;
				if ($i > 0) {
					if ($emailCsvContent['0'] == $orderIncrementId) {
						$flag = false;
					}
				}
				$i++;
			}

			fclose ($file);

			if ($flag) {
				$emailCsvContentArray = array_filter($emailCsvContentArray);
				if (empty($emailCsvContentArray)) {
					$emailCsvContentHeader[] = "Order Number";
					$emailCsvContentHeader[] = "Customer Email";

					array_push($emailCsvContentArray, $emailCsvContentHeader);
				}
				$emailCsvNewContent[] = $orderIncrementId;
				$emailCsvNewContent[] = $customerEmail;

				array_push($emailCsvContentArray, $emailCsvNewContent);

				$file = fopen($emailCsvFile,"w");

				foreach ($emailCsvContentArray as $emailCsvContentRow)
				{
					fputcsv($file, $emailCsvContentRow);
				}

				fclose ($file);
			}
		} else {
			$emailCsvContentArray = array();

			$emailCsvContentHeader[] = "Order Number";
			$emailCsvContentHeader[] = "Customer Email";
			array_push($emailCsvContentArray, $emailCsvContentHeader);

			$emailCsvNewContent[] = $orderIncrementId;
			$emailCsvNewContent[] = $customerEmail;
			array_push($emailCsvContentArray, $emailCsvNewContent);

			$file = fopen($emailCsvFile,"w");

			foreach ($emailCsvContentArray as $emailCsvContentRow)
			{
				fputcsv($file, $emailCsvContentRow);
			}

			fclose ($file);
		}
	}

	function getMediaClipProductName($sku, $module){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$modulename = $objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection()->addFieldToFilter('module_name', $module);
		$module = $modulename->getFirstItem()->getData('id');
		$c = ikf_product_c();
		$c->addFieldToFilter(mProduct::F__PLU, $sku);
		$c->addFieldToFilter('module', $module);
		$_mediaclip_product = $c->getFirstItem();
		$label = false;

		if ($_mediaclip_product) {
			$label = trim($_mediaclip_product->getProductLabel());
		}

		return $label;
	}
	function getProjectDirectoryPath($label, $orderDirectoryPath){
		$folder_name = preg_replace('/\s+/', ' ', $label);
		$projectDirectoryPath = $this->createOrderLineDirectory($orderDirectoryPath, $folder_name);
		return $projectDirectoryPath;
	}
	function createOrderLineDirectory($dirPath, $dirName){
		$dirname = $this->getDirName($dirPath, $dirName);
		$uploadToMe = $dirPath.'/'.$dirname;
		if(!is_dir($uploadToMe)){
			mkdir($uploadToMe, 0777);
			return $uploadToMe;
		}
	}

	function getDirName($dirPath, $dirName){
		$newpath = $dirPath.'/'.$dirName;
		$newname = $dirName;
		$counter = 0;
		while (is_dir($newpath)) {
		   $newname = $dirName .'-'. $counter;
		   $newpath = $dirPath.'/'.$newname;
		   $counter++;
		 }

		return $newname;
	}

	function getfileNewname($path, $filename){
		if ($pos = strrpos($filename, '.')) {
		   $name = substr($filename, 0, $pos);
		   $ext = substr($filename, $pos);
		} else {
			$name = $filename;
		}

		$newpath = $path.'/'.$filename;
		$newname = $filename;
		$counter = 1;
		while (file_exists($newpath)) {
			$newname = $name .'-'. $counter . $ext;
			$newpath = $path.'/'.$newname;
			$counter++;
		}

		return $newname;
	}

	function createFileCopy($filePath, $fileName, $numberofcopies){
		$sourceFile = $filePath.'/'.$fileName;
		$newfileName = $fileName;
		for ($i=1; $i < $numberofcopies; $i++) {
			$newfilePath = $filePath.'/'.$this->getfileNewname($filePath, $newfileName);
			if (!copy($sourceFile, $newfilePath));
		}
	}
}