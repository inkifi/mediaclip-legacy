<?php 


namespace Mangoit\MediaclipHub\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class GetProductsPriceEndpoint extends Action
{
        /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $logger;
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Psr\Log\LoggerInterface $logger,
        PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        parent::__construct($context);
 
    }
 
    public function execute()
    {

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
