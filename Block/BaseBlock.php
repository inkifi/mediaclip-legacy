<?php
/**
 * Copyright © 2015 Mangoit . All rights reserved.
 */
namespace Mangoit\MediaclipHub\Block;

use Magento\Framework\UrlFactory;

class BaseBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mangoit\MediaclipHub\Helper\Data
     */
    protected $_devToolHelper;
     
     /**
      * @var \Magento\Framework\Url
      */
    protected $_urlApp;
     
     /**
      * @var \Mangoit\MediaclipHub\Model\Config
      */
    protected $_config;

    /**
     * @param \Mangoit\MediaclipHub\Block\Context $context
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    function __construct( \Mangoit\MediaclipHub\Block\Context $context
    )
    {
        $this->_devToolHelper = $context->getMediaclipHubHelper();
        $this->_config = $context->getConfig();
        $this->_urlApp=$context->getUrlFactory()->create();
        parent::__construct($context);
    }
    
    /**
     * Function for getting event details
     * @return array
     */
    function getEventDetails()
    {
        return  $this->_devToolHelper->getEventDetails();
    }
    
    /**
     * Function for getting current url
     * @return string
     */
    function getCurrentUrl()
    {
        return $this->_urlApp->getCurrentUrl();
    }
    
    /**
     * Function for getting controller url for given router path
     * @param string $routePath
     * @return string
     */
    function getControllerUrl($routePath)
    {
        
        return $this->_urlApp->getUrl($routePath);
    }
    
    /**
     * Function for getting current url
     * @param string $path
     * @return string
     */
    function getConfigValue($path)
    {
        return $this->_config->getCurrentStoreConfigValue($path);
    }
    
    /**
     * Function canShowMediaclipHub
     * @return bool
     */
    function canShowMediaclipHub()
    {
        $isEnabled=$this->getConfigValue('mediacliphub/module/is_enabled');
        if ($isEnabled) {
            $allowedIps=$this->getConfigValue('mediacliphub/module/allowed_ip');
            if (is_null($allowedIps)) {
                return true;
            } else {
                $remoteIp=$_SERVER['REMOTE_ADDR'];
                if (strpos($allowedIps, $remoteIp) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}
