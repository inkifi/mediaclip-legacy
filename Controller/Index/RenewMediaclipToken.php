<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mangoit\MediaclipHub\Session as mSession;
class RenewMediaclipToken extends Action {
	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	function __construct(Context $context, PageFactory $resultPageFactory) {
		$this->_resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	/**
	 * 2018-09-11
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
	 */
	function execute() {
		$s = df_customer_session(); /** @var Session|mSession $s */
		if ($to = $s->getMediaClipToken()) { /** @var object|null to */
			$hubHelperResponse = $this->_objectManager->create('Mangoit\MediaclipHub\Helper\Data')->RenewToken($to->token);
			if (!empty($hubHelperResponse)) {
				$s->setMediaClipToken($hubHelperResponse);
				return $hubHelperResponse;
			} else {
				die('42');
			}
		}
	}

	/** @var Magento\Framework\View\Result\PageFactory */
	protected $_resultPageFactory;
}