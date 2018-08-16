<?php
/**
 * Copyright © 2015 Mangoit . All rights reserved.
 */
namespace Mangoit\MediaclipHub\Helper;
// 2018-08-17 Dmitry Fedyuk
// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
use Inkifi\Core\Settings as S;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

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

	public function __construct(\Magento\Framework\App\Helper\Context $context,\Magento\Framework\UrlInterface $response
	) {
        $this->response = $response;
		parent::__construct($context);
	}

	public function getModules()
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
	public function getThemes()
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
    public function getSuppliers()
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
    public function getDustjacketpopup()
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
    public function getuploadFolder()
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
    public function getPhotobookProduct()
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

                        $arr['value'] = $option['plu'];
                        $arr['label'] = $option['product_label'];
                        $finalOptions[] = $arr;
                    }
                }
            }
        }
        return $finalOptions;
    }
    public function getGiftingProduct()
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

                        $arr['value'] = $option['plu'];
                        $arr['label'] = $option['product_label'];
                        $finalOptions[] = $arr;
                    }
                }
            }
        }
       
        return $finalOptions;
    }
    public function getPrintProduct()
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

                        $arr['value'] = $option['plu'];
                        $arr['label'] = $option['product_label'];
                        $finalOptions[] = $arr;
                    }
                }
            }
        }
        return $finalOptions;
	}

    public function getEditorLinkUrl($product, $additional = array())
    { 

        return $this->getEditorUrl($product, $additional);
    }

    public function getEditorUrl($product, $additional = array())
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


    public function getGUID(){
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
    public function getCustomerId()
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
    private  function BuildCurl($method, $url, $authorization, $data = false)
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
        $checkoutWriter = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout.log');
        $checkoutLogger = new \Zend\Log\Logger();
        $checkoutLogger->addWriter($checkoutWriter);
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

    private function GetStoreAuthorizationHeader() {
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
        $this->STOREAPPID = S::s()->id();
        $this->STORESECRET = S::s()->key();
        $authorizationHeader = 'HubApi ' . base64_encode($this->STOREAPPID . ":" . $this->STORESECRET);
        return $authorizationHeader;
    }
    
    public function checkUserToken($postData){

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
            if ($currentUserId == $projectUserId) {
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
    //The end user token has a short life span. A token can be extended.
    public  function RenewToken($userToken)
    {
        $postRequestBody = json_decode(@file_get_contents('php://input'));
        if ($postRequestBody->token != $userToken)
        {
            throw new Exception("Cannot renew token because it differs from expected token");
        }
        $this->HUBURL = S::s()->url();
        $curl = $this->BuildCurl("POST", $this->HUBURL . "/auth/jwt/renew", $this->GetStoreAuthorizationHeader(), $postRequestBody);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($httpCode != 200)
        {
            return array();
            //self::ThrowHttpException("Could not renew Hub user token", $httpCode, $response);
        }
        $userTokenInfo = json_decode($response);
        return $userTokenInfo;
    }
    public function getMediaClipUserToken(){
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
    public  function CheckoutWithSingleProduct($postRequestBody)
    {
       // print_r(json_encode($postRequestBody)); die();
        $checkoutWriter = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout.log');
        $checkoutLogger = new \Zend\Log\Logger();
        $checkoutLogger->addWriter($checkoutWriter);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/helper.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode($postRequestBody));
        $checkoutLogger->info(json_encode($postRequestBody));
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
        $this->HUBURL = S::s()->url();
        $this->STOREAPPID = S::s()->id();
        $curl = $this->BuildCurl("POST", $this->HUBURL . "/stores/" . $this->STOREAPPID . "/orders", $this->GetStoreAuthorizationHeader(), $postRequestBody);
        $response = curl_exec($curl);
        
        $checkoutLogger->info(json_encode($response));

        if($response) {
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            sleep(0.3);
            try {
                    if ($response && $httpCode == 201){   
                        $checkoutInformation = json_decode($response, true);
                        
                        $logger->info($response);
                        return $checkoutInformation;
                    }
                    $logger->info($response);
                } catch (\Exception $e) {
                    $checkoutLogger->info(
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
    }
    public function consolidateCustomerAndGetCustomerToken($storeUserId, $anonymousCustomerId)
    {
        
        $checkoutWriter = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout_login.log');
        $checkoutLogger = new \Zend\Log\Logger();
        $checkoutLogger->addWriter($checkoutWriter);

        $postRequestBody['storeData'] = array("anonymousUserId" => $anonymousCustomerId);
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
        $this->HUBURL = S::s()->url();
        $endPoint = $this->HUBURL."/stores/".S::s()->id()."/users/".$storeUserId."/consolidation";
        $curl = $this->BuildCurl("POST", $endPoint, $this->GetStoreAuthorizationHeader(), $postRequestBody);
        $response = curl_exec($curl);
        /* Consolidate customer response */
        $checkoutLogger->info( "====Request for login ====" );
        $checkoutLogger->info(
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

        $checkoutLogger->info( "====Response for login ====" );
        $checkoutLogger->info($response);


        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);

        /*if ($httpCode != 201)
        {
            self::ThrowHttpException("Could not create Hub user token", $httpCode, $response);
        }*/


        $userTokenInfo = json_decode($response);
        
        return $userTokenInfo;
    }
    public function getMediaClipProjects($storeUserId) {
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

    public function saveMediaclipOrder($postData){
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

	/**
	 * 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "It looks like the «Mediaclip Order Status» tab on a backend order page is always empty
	 * because it is wrongly programmed":
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/2
	 * @param $orderId
	 * @return bool
	 */
    function downloadAndUploadOrderFilesToServer($orderId){
        //$hubHelper = Mage::helper('zalw_mediaclub/hub');
        $response = false;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $orderIncrementId = $order->getIncrementId();
        $orderId = $order->getEntityId();
        $orderDate = $order->getCreatedAt();
        $csvContent['order_increment_id'] = $orderIncrementId;
        $csvContent['promotion_code'] = $order->getCuponCode();
        $shipping_address = $order->getShippingAddress()->getData();

        $name = $shipping_address['firstname']." ".$shipping_address['lastname'];
        $csvContent['customer_name'] = $name;

        $street = trim(preg_replace('/\s+/', ' ', $shipping_address['street']));
        $street = '"'.$street.'"';
        $csvContent['street'] = $street;

        $csvContent['city'] = $shipping_address['city'];
        
        $country_id = $shipping_address['country_id'];
        $country = $objectManager->create('\Magento\Directory\Model\Country')->loadByCode($country_id);
        $country = $country->getName();
        //$country = Mage::app()->getLocale()->getCountryTranslation($country_id);
        $csvContent['country'] = $country;

        $csvContent['postcode'] = $shipping_address['postcode'];
        $csvContent['telephone'] = $shipping_address['telephone'];
        $csvContent['email'] = $order->getCustomerEmail();

        $mediaclipOrderDetails = $this->getMediaClipOrders($orderId);
        $linesArray = array();
        foreach ($mediaclipOrderDetails->lines as $lines) {
            if ($lines->status->value == 'AvailableForDownload') {
                $product_id = $lines->storeData->productId;
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
                $attributeSet = $objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
                $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
                $attribute_set_name = $attributeSetRepository->getAttributeSetName();
                
                if ($product->getAttributeSetId() != 4) {
                    //echo "<br> Product Module ".$product->getMediaclipModule();
                    if ($attribute_set_name == 'Print' || $attribute_set_name == 'Gifting') {
                        $projectId = $lines->projectId;
                        $projectData = $objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($projectId, 'project_id')->getData();
                        $projectDetails = json_decode($projectData['project_details'], true);
                        
                        if (isset($projectDetails['properties']['option_details'])) {
                            $itemoptions = json_decode($projectDetails['properties']['option_details'], true);
                            
                            $productOptions = $product->getOptions();
                            if ($productOptions) {
                                foreach($productOptions as $optionDetail){
                                    if (isset($itemoptions[$optionDetail->getData('option_id')])) {
                                        $optionValues = $optionDetail->getValues();
                                        foreach ($optionValues as $_value) {
                                            if ($_value->getOptionTypeId() == $itemoptions[$optionDetail->getData('option_id')]) {
                                                if($_value->getSku() != ''){
                                                    $linesArray[$lines->id]['product_sku'] = $_value->getSku();
                                                    $linesArray[$lines->id]['product_module'] = $attribute_set_name;
                                                    $linesArray[$lines->id]['upload_folder'] = $product->getMediaclipUploadFolder();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            
                        } else {
                            if ($attribute_set_name == 'Print' && $product->getMediaclipPrintProduct()) {
                                $linesArray[$lines->id]['product_sku'] = $product->getMediaclipPrintProduct();
                                $linesArray[$lines->id]['product_module'] = $attribute_set_name;
                                $linesArray[$lines->id]['upload_folder'] = $product->getMediaclipUploadFolder();
                            } else if ($attribute_set_name == 'Gifting' && $product->getMediaclipGiftingProduct()) {
                                $linesArray[$lines->id]['product_sku'] = $product->getMediaclipGiftingProduct();
                                $linesArray[$lines->id]['product_module'] = $attribute_set_name;
                                $linesArray[$lines->id]['upload_folder'] = $product->getMediaclipUploadFolder();
                            }
                        }
                    }
                }
            }
        }
        if (!empty($linesArray)) {
            $linesCount = count($linesArray);
            $subDir = true;
            /*if ($linesCount > 1) {
            } else {
                $subDir = true;
            }*/
             $csv = "Order Number,Order Item ,Product Type,Quantity,Name,Address 1,Address 2,City,Country, PostCode,Telephone,Customer Email,Promotion Code,Option: Images\n";

            foreach ($linesArray as $lineId => $lineData) {
                $mediaclipProductSku = $lineData['product_sku'];
                $mediaclipProductModule = $lineData['product_module'];
                $uploadFolderSupplier = $lineData['upload_folder'];

                //$orderDirectoryPath = $this->createOrderDirectory($orderIncrementId, $orderDate, $uploadFolderSupplier);

               //$this->uploadEmailsCSV($this->supplierFolderPath, $orderIncrementId, $csvContent['email']);

                $linesDetails = $this->getMediaClipOrderLinesDetails($lineId);
                $projectId = $linesDetails->projectId;

                foreach ($order->getAllItems() as $orderitem){
                    if ($orderitem->getMediaclipProjectId() == $projectId) {
                        $orderItemQuantity = $orderitem->getQtyOrdered();
                        $orderItemID = $orderitem->getItemId();
                    }
                }
                $orderDirectoryPath = $this->createOrderDirectory($orderIncrementId, $orderDate, $uploadFolderSupplier,$orderItemID);
                
                $this->uploadEmailsCSV($this->supplierFolderPath, $orderIncrementId, $csvContent['email']);
                
                $product_type = $this->getMediaClipProductName($mediaclipProductSku, $mediaclipProductModule);
                $number_of_images = 0;

                $filesUploadPath = $orderDirectoryPath;

                if ($subDir) {
                    $filesUploadPath = $this->getProjectDirectoryPath($product_type, $filesUploadPath);
                }

                foreach ($linesDetails->files as $fileDetails) {
                    $quantityToCopy = $fileDetails->quantity;
                    $fileUrl = $fileDetails->url;

                    //$fileBasename = basename($fileUrl);
                    if ($url = parse_url($fileUrl)) {
                       $fileBasename = pathinfo($url['path'], PATHINFO_BASENAME);
                    } else {
                        $fileBasename = basename($fileUrl);
                    }

                    $fileNewname = $this->getfileNewname($filesUploadPath, $fileBasename);

                    $fileContent = file_get_contents($fileUrl);
                    file_put_contents($filesUploadPath.'/'.$fileNewname, $fileContent);

                    if ($quantityToCopy > 1) {
                        $this->createFileCopy($filesUploadPath, $fileBasename, $quantityToCopy);
                    }
                    $number_of_images = $number_of_images + $quantityToCopy;
                }
                $csvFileName = $orderIncrementId.'.csv';
                $csvpath = substr($orderDirectoryPath,0,strrpos($orderDirectoryPath,'/'));
                $csvFile = $csvpath.'/'.$csvFileName;

                /*$csvFileName = $orderIncrementId.'.csv';
                $csvFile = $filesUploadPath.'/'.$csvFileName;*/

               

                $csv .= $csvContent['order_increment_id'].','.$orderItemID.','.$product_type.','.$orderItemQuantity.','.$csvContent['customer_name'].','.$csvContent['street'].','.' '.','.$csvContent['city'].','.$csvContent['country'].','.$csvContent['postcode'].','.$csvContent['telephone'].','.$csvContent['email'].','.$csvContent['promotion_code'].','.$number_of_images."\n";

                $id = $orderItemID;
                $csv_handler = fopen ($csvFile, 'w+');
                //$file_handle = fopen("testimonials.csv", "w+");
                $myCsv = array();
                while (!feof($csv_handler) ) {
                    $line_of_text = fgetcsv($csv_handler, 1024);   
                    
                    if ($id != $line_of_text[1] && $line_of_text !== false ) {
                        fputcsv($csv_handler, $line_of_text);
                    }
                }
                fwrite ($csv_handler, $csv);
                fclose ($csv_handler);
                $response = true;
            }
        }
        return $response;
    }
    public function createOrderDirectoryDate($order_created_at)
    {
        $order_day = date('D', strtotime($order_created_at));
        $present = strtotime($order_created_at);
        $folder_name = date('d-m-Y', $present);
        return $folder_name;
    }
    public function createOrderDirectory($orderId, $order_created_at, $supplierDirName , $orderItemID){
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
    public function checkDir($dirPath , $supplierDirName ='' ){
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
    
    function getMediaClipOrders($storeOrderId) {
        //GET https://api.mediacliphub.com/stores/{YOUR-KEY}/orders/{YOUR-ORDER-ID}
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
        $service_url = S::s()->url().'/stores/'.S::s()->id().'/orders/'.$storeOrderId;
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
    public function createDirectory($path){
        if(!is_dir($path)){
            mkdir($path, 0777);
        }   
    }
    public function uploadEmailsCSV($supplierFolderPath, $orderIncrementId, $customerEmail){
        
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

    function getMediaClipOrderLinesDetails($storeOrderLineId){
        //GET https://api.mediacliphub.com/stores/{YOUR-KEY}/orders/{YOUR-ORDER-ID}
		// 2018-08-17 Dmitry Fedyuk
		// «Force Mediaclip to use the relevant API credentials in the multi-store mode»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/4
        $service_url = S::s()->url().'/lines/'.$storeOrderLineId;
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
    public function getMediaClipProductName($sku, $module){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $modulename = $objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection()->addFieldToFilter('module_name', $module);
        $module = $modulename->getFirstItem()->getData('id');
        $_mediaclip_product = $objectManager->create('Mangoit\MediaclipHub\Model\Product')->getCollection()->addFieldToFilter('plu', $sku)->addFieldToFilter('module', $module)->getFirstItem();
        $label = false;

        if ($_mediaclip_product) {
            $label = trim($_mediaclip_product->getProductLabel());
        }
       
        return $label;
    }
    public function getProjectDirectoryPath($label, $orderDirectoryPath){
        $folder_name = preg_replace('/\s+/', ' ', $label);
        $projectDirectoryPath = $this->createOrderLineDirectory($orderDirectoryPath, $folder_name);
        return $projectDirectoryPath;
    }
    public function createOrderLineDirectory($dirPath, $dirName){
        $dirname = $this->getDirName($dirPath, $dirName);
        $uploadToMe = $dirPath.'/'.$dirname;
        if(!is_dir($uploadToMe)){
            mkdir($uploadToMe, 0777);
            return $uploadToMe;
        }
    }

    public function getDirName($dirPath, $dirName){
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

    public function getfileNewname($path, $filename){
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

    public function createFileCopy($filePath, $fileName, $numberofcopies){
        $sourceFile = $filePath.'/'.$fileName;
        $newfileName = $fileName;
        for ($i=1; $i < $numberofcopies; $i++) { 
            $newfilePath = $filePath.'/'.$this->getfileNewname($filePath, $newfileName);
            if (!copy($sourceFile, $newfilePath));
        }
    }
}