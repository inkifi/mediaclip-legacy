<?php

namespace Mangoit\MediaclipHub\Block\Mediaclip;

class Projects extends \Magento\Framework\View\Element\Template
{
	protected $_projects;

	function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}

	/**
	 * 2020-03-04
	 * @return array
	 * @throws \Exception
	 */
    function getProjects() {
        $hubHelper = mc_h();
        return $hubHelper->getMediaClipProjects($hubHelper->getCustomerId());
    }

    function getProjectEditUrl($project_id){

        $url = "javascript:void(0)";
        if ($project_id) {
        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $projectDetail = $objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->getCollection()->addFieldToFilter('project_id', $project_id)->getData();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        	$store = $storeManager->getStore();
          //echo "<pre>";  print_r($projectDetail);
        	//$customizeProjectUrl = $store->getBaseUrl().'mediacliphub/index/customizeproject/'."projectId/".$projectId."/mode/".$mode;
            if (!empty($projectDetail)) {
                $url = $store->getUrl('mediacliphub/product/edit', array('product' => $projectDetail[0]['store_product_id'], 'mode' => 'editCartProject', 'projectId' => $project_id));
            }
        }
        return $url;
    }

    function checkCustomerLoggedIn(){
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get('Magento\Customer\Model\Session')->isLoggedIn();
    }
}
