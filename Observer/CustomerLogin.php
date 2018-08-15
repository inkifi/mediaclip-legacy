<?php

namespace Mangoit\MediaclipHub\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $customer = $observer->getEvent()->getCustomer();
        //Mage::app()->cleanCache();
        //Mage::dispatchEvent('adminhtml_cache_flush_system');

        //$customer = $observer->getCustomer();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $session = $objectManager->get('Magento\Customer\Model\Session');
        
        $storeUserId = $customer->getEntityId();
        $hubHelper = $objectManager->create('Mangoit\MediaclipHub\Helper\Data');

        $userConsolidationWriter = new \Zend\Log\Writer\Stream(BP . '/var/log/user_login_consolidation.log');
        $userConsolidatedLog = new \Zend\Log\Logger();
        $userConsolidatedLog->addWriter($userConsolidationWriter);


        
        if ($session->getMediaClipUserId() && $session->getMediaClipUserType() == 'anonymous') {
            $anonymousCustomerId = $session->getMediaClipUserId();
            $userConsolidatedLog->info("==============Anonymous User==================");
            try{

                $token = $hubHelper->consolidateCustomerAndGetCustomerToken($storeUserId, $anonymousCustomerId);

                $userConsolidatedLog->info(
                    json_encode(
                        array(
                            "Response token" =>array(
                                "storeUserId"=>$storeUserId,
                                "anonymous customer id" => $anonymousCustomerId,
                                "Token"=>$token
                            )
                            
                        ),
                        JSON_PRETTY_PRINT
                    )
                );

                

                $quote = $objectManager->create('\Magento\Checkout\Model\Cart')->getQuote();
                if ($quote->getAllVisibleItems()) {
                    foreach ($quote->getAllVisibleItems() as $item) {
                        if ($item->getMediaclipProjectId()) {
                            $model = $objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($item->getMediaclipProjectId(), 'project_id');
                            $project_details = $model->getData();
                            if (!empty($project_details)) {
                                if($project_details['user_id'] == $anonymousCustomerId){
                                    $project_details['user_id'] = $storeUserId;
                                    if (isset($project_details['project_details']) && $project_details['project_details'] != '') {
                                        $projectDetails = json_decode($project_details['project_details'], true);
                                        $projectDetails['storeData']['userId'] = $storeUserId;
                                        $project_details['project_details'] = json_encode($projectDetails);
                                    }
                                }
                                $model->setData($project_details)->save();
                            }
                        }
                    }
                } else {
                    $collection = $objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->getCollection()->addFieldToFilter('user_id', $anonymousCustomerId);
                    foreach ($collection->getData() as $project_details) {
                        if($project_details['user_id'] == $anonymousCustomerId){
                            $project_details['user_id'] = $storeUserId;
                            if (isset($project_details['project_details']) && $project_details['project_details'] != '') {
                                $projectDetails = json_decode($project_details['project_details'], true);
                                $projectDetails['storeData']['userId'] = $storeUserId;
                                $project_details['project_details'] = json_encode($projectDetails);
                            }
                        }
                        $model = $objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->load($project_details['id'], 'project_id');
                        $model->setData($project_details)->save();
                    }
                }
            }
            catch(Exception $e){
                echo $e->getMessage();
                die;
            }
            $storeUserId = $hubHelper->getCustomerId();
            //$session->setMediaClipToken($token);

            $userToken = $hubHelper->HandleUserToken($storeUserId, $token);
            $session->setMediaClipToken($userToken);
            $session->setMediaClipUserId($storeUserId);
            $session->setMediaClipUserType('registered');
            /*$storeUserId = $helper->getCustomerId();
            //$session->setMediaClipToken($token);

            $userToken = $helper->HandleUserToken($storeUserId, $token);
            $session->setMediaClipToken($userToken);
            $session->setMediaClipUserId($storeUserId);
            $session->setMediaClipUserType('registered');*/
           
        }else{

            $helper = $objectManager->create('Mangoit\MediaclipHub\Helper\Data');
            $storeUserId = $helper->getCustomerId();
            $session = $objectManager->get('Magento\Customer\Model\Session');
            $anonymousCustomerId = $session->getMediaClipUserId();
            $userToken = $helper->HandleUserToken($storeUserId, $session->getMediaClipToken());
            $session->setMediaClipToken($userToken);
            
            //$session->setMediaClipToken($token);
            $session->setMediaClipUserId($storeUserId);
        }
    }
}