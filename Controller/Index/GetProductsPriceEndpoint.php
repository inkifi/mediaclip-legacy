<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class GetProductsPriceEndpoint extends Action {
	protected $logger;
	/**
	 * @param Context     $context
	 * @param PageFactory $resultPageFactory
	 */
	function __construct(Context $context, \Psr\Log\LoggerInterface $logger, PageFactory $resultPageFactory) {
		$this->logger = $logger;
		parent::__construct($context);
	}

	/** 2019-05-14 */
	function execute() {
		$json = file_get_contents('php://input');
		$obj = json_decode($json, true);
		$this->logger->info('$obj=>productpriceEndpoint');
		/**
		 * 2018-07-29 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Notice: Array to string conversion»
		 * The previous code was:
		 * 		$this->logger->info($obj);
		 */
		$this->logger->info(json_encode($obj, JSON_PRETTY_PRINT));
		foreach ($obj['items'] as $key => $value) {
			$newArray = array();
			$productId = $value['productId'];
			$newArray['productId'] = "$productId";
			$newArray['price']['original'] = "$0";
			$newArray['price']['value'] = 0;
			$response[] = $newArray;
		}
		echo json_encode($response);
	}
}
