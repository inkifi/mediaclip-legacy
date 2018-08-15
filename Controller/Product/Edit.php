<?php 


namespace Mangoit\MediaclipHub\Controller\Product;
 
use Magento\Framework\Controller\ResultFactory; 
class Edit extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
/**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $response;
    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\UrlInterface $response,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->response = $response;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {   
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $refererUrl = $this->_redirect->getRefererUrl();

        $productId  = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->getResponse()->setRedirect($refererUrl);
            return $this;
        }

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
     
        if (!$product->getId()) {
            
            $this->getResponse()->setRedirect($refererUrl);
            return $this;
        }
        $session =   $this->_objectManager->get('Magento\Customer\Model\Session');
        $session->setProduct($product);
        $mode = $this->getRequest()->getParam('mode');
        if (!$mode) {
            $mode = 'newProject';
            // load mediaclip module
            $attributeSet = $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
            $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
            $mediaclip_module = $attributeSetRepository->getAttributeSetName();

            if (!$mediaclip_module) {
                $this->getResponse()->setRedirect($refererUrl);
                return $this;
            }

            $mediaclip_module = ucfirst(strtolower($mediaclip_module));
            $model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Modules');
            //$mediaclip_module = $model->getMediaClipModuleName($mediaclip_module);
            
            $mediaclip_product = $this->getMediaClipProduct($product, $mediaclip_module);
            
            if (!$mediaclip_product) {
                $this->getResponse()->setRedirect($refererUrl);
                return $this;
            }

            $session->setMediaclipProduct($mediaclip_product);
            $mediaclip_theme_url = $mediaclip_product['product_theme'];

            if (!$mediaclip_theme_url) {
                $mediaclip_theme_url = NULL;
            }

            $mediaclip_product_id = $mediaclip_product['product_id'];
            $additionalProprties = array();
            $storeProductId = $product->getId();

            $product_options = $this->getStepData($storeProductId);

            if (isset($product_options['options'])) {
                $additionalProprties['option_details'] = json_encode($product_options['options']);
            }

            $helper = $this->_objectManager->create('Mangoit\MediaclipHub\Helper\Data');

            $storeUserId = $helper->getCustomerId();
            
            $checkoutWriter = new \Zend\Log\Writer\Stream(BP . '/var/log/request_token.log');
            $checkoutLogger = new \Zend\Log\Logger();
            $checkoutLogger->addWriter($checkoutWriter);


        

            //$session = Mage::getSingleton('core/session');
            /*print_r($storeUserId);
            print_r('btw');
            print_r($session->getMediaClipToken());*/
            //die('103');
            $userToken = $helper->HandleUserToken($storeUserId, $session->getMediaClipToken());
            $checkoutLogger->info(
                        json_encode(
                            array(
                            "Request Token : "=>    $userToken
                            )
                        )
                    );
            $session->setMediaClipToken($userToken);
            
            $mediaclip_product_date_options = false;
            //if ($_SERVER['REMOTE_ADDR'] == '103.231.46.194') {
                $mediaclip_product_date_options = $this->getMediaClipProductDateOptions($product);
            //}
            
            $projectId = $helper->CreateProject($mediaclip_module, $mediaclip_product_id, $mediaclip_theme_url, $storeProductId, $userToken->token, $additionalProprties, $mediaclip_product_date_options);

            $session->setMediaClipProjectId($projectId);

            $mediaClipData['project_id'] = $projectId;
            $mediaClipData['user_id'] = $storeUserId;
            $mediaClipData['store_product_id'] = $storeProductId;
            $mediaClipData['project_details'] = '';

            $model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->setData($mediaClipData);
            $model->save();
        }else{

            $storeProductId = $product->getId();

            $projectId = $this->getRequest()->getParam('projectId');

            $session = $this->_objectManager->get('Magento\Customer\Model\Session');

            $session->setMediaClipProjectId($projectId);
            $model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($projectId, 'project_id');
                $project_details = $model->getProjectDetails();
                if (isset($project_details['properties']['option_details'])) {
                    
                    if ($project_details) {
                        $project_details = json_decode($project_details, true);
                        $optionsDetails = json_decode($project_details['properties']['option_details'], true);
                    }
                   //echo "<pre>"; print_r($optionsDetails);
                    $options = $product->load($productId)->getOptions();
                    if ($optionsDetails) {
                        
                        
                        $productUrl = $product->load($productId)->getProductUrl();
                        $productOptionsId = array();
                        foreach ($options as $key => $value) {
                            $optionTitle = $value->getTitle();
                            if ($optionTitle == 'Project' || $optionTitle == 'Additional' ) {
                                continue;   
                            }
                            $productOptionsId[] = $key ;
                        }
                        $insectedArray = array_intersect_key(array_flip($productOptionsId),$optionsDetails);
                        if (count($insectedArray) != count($productOptionsId)) {
                            $customizeProjectUrl = $productUrl ;
                            $resultRedirect = $this->resultRedirectFactory->create();
                            $resultRedirect->setPath($customizeProjectUrl);
                            return $resultRedirect;
                        }
                    }
                }
        }
        //return $this->response->getUrl('mediacliphub/product/edit', $routeParams);

        
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $customizeProjectUrl = $store->getBaseUrl().'mediacliphub/index/customizeproject/'."projectId/".$projectId."/mode/".$mode;
        
        //$customizeProjectUrl = $this->getUrl('mediacliphub/index/customizeproject')."projectId/".$projectId."/mode/".$mode;
        //$this->response->getUrl($customizeProjectUrl);
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($customizeProjectUrl);
        return $resultRedirect;
        /*$resultRedirect->setPath($customizeProjectUrl);
        return $this->resultPageFactory->create();*/
    }
    public function getMediaClipModule($_product){
       
        $option_id = $_product->getMediaclipModule();
        $mediaclipModule = NULL;
        if ($option_id  > 0) {
            $attr = 'mediaclip_module';
            $attr = $_product->getResource()->getAttribute($attr);
            if ($attr->usesSource()) {
                $mediaclipModule = $attr->getSource()->getOptionText($option_id);
            }
        }
        return $mediaclipModule;
    }
    public function getModuleID($module_name){
        $model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Modules')->load($module_name, 'module_name');
        $module_id = $model->getData('id');
        
        return $module_id;
    }

    public function getMediaClipProduct($_product, $_module = false){
        $stepData = $this->getStepData($_product->getId());
        

        $attributeSet = $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
        $attributeSetRepository = $attributeSet->get($_product->getAttributeSetId());
        $mediaclipModule = $attributeSetRepository->getAttributeSetName();
        //$mediaclipModule = $_product->getMediaClipModule();
        $productOptions = $_product->getOptions();

        if ($stepData) {
            if (isset($stepData['options'])) {
                $mediaclip_product = $this->getMediaClipProductSku($stepData, $productOptions, $_module);
                print_r($mediaclip_product);
                if (!$mediaclip_product) {
                    $mediaclip_product = $this->getMediaclipProductData($_product, $_module);
                    $mediaclip_product = (empty($mediaclip_product)) ? false : $mediaclip_product[0];
                }
                return $mediaclip_product;
            } else {
                $mediaclip_product = $this->getMediaclipProductData($_product, $_module);
                $mediaclip_product = (empty($mediaclip_product)) ? false : $mediaclip_product[0];
                    return $mediaclip_product;
            }
        }
        return false;
    }

    public function getStepData($productId){
        //$session = Mage::getSingleton('checkout/session');
        //$stepData = $session->getStepData(self::SESSIONSTEP);
        $stepData = $this->getRequest()->getParams();

        if (!empty($stepData)) {
            if (isset($stepData['options'])) {
                $stepData['options'] = json_decode($stepData['options']);
            }
            if (isset($stepData['product']) && $stepData['product'] == $productId) {
                return $stepData;
            }
        }
        return false;
    }

    public function getMediaClipProductSku($stepData, $productOptions, $_module){
        
        foreach ($stepData['options'] as $oid => $ovalue) {
            if ($productOptions) {
                
                foreach($productOptions as $optionId => $optionDetail){

                    if ($oid == $optionDetail->getOptionId()) {
                    
                        
                        $optionValues = $optionDetail->getValues();
                        if (is_array($optionValues)) {
                            foreach ($optionValues as $vid => $_value) {
                                if ($ovalue == $_value->getOptionTypeId()) {
                                    
                                    if ($_value->getSku()) {
                                        $model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Product');
                                        $_module = $this->getModuleID($_module);

                                        $mediaclip_product = $model->getMediaClipProductBySku($_value->getSku(), $_module);
                                        $mediaclip_product = (empty($mediaclip_product)) ? false : $mediaclip_product[0];
                                        return $mediaclip_product;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public function getMediaclipProductData($_product, $_module){
       
        if ($_module == 'Photobook') {
            $option_id = $_product->getMediaclipPhotobookProduct();
        } else if ($_module == 'Gifting') {
            $option_id = $_product->getMediaclipGiftingProduct();
        } else if ($_module == 'Print') {
            $option_id = $_product->getMediaclipPrintProduct();
        }
        if ($option_id) {
            $_module = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Modules')->getCollection()->addFieldToFilter('module_name', $_module)->getData();
            foreach ($_module as  $value) {
                $val['id'] = $value['id'];
            }

            $collection = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Product')->getCollection()->addFieldToFilter('plu', $option_id)->addFieldToFilter('module', $val['id'])->getData();
            return $collection;
        }
        return false;
    }

    public function getMediaClipProductDateOptions($_product){
        $productId = $_product->getId();
        $stepData = $this->getStepData($productId);
        $mediaClipProductDateOptions = false;
        if ($stepData) {
            if (isset($stepData['options'])) {
                $productOptions = $_product->getOptions();
                foreach ($stepData['options'] as $oid => $ovalue) {
                    if ($productOptions) {
                        foreach($productOptions as $optionId => $optionDetail){
                            if ($oid == $optionDetail->getOptionId()) {
                                if ($optionDetail->getType() == 'date') {
                                    $ovalueArray = (array)$ovalue;
                                    if (isset($ovalueArray['month']) && isset($ovalueArray['year'])) {
                                        $mediaClipProductDateOptions['startYear'] = $ovalueArray['year'];
                                        $mediaClipProductDateOptions['startMonth'] = $ovalueArray['month'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $mediaClipProductDateOptions;
    }
}

