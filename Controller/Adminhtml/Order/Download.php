<?php

namespace Mangoit\MediaclipHub\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Download extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * Generate order history for ajax request
	 * 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "It looks like the «Mediaclip Order Status» tab on a backend order page is always empty
	 * because it is wrongly programmed":
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/2
     * @return \Magento\Framework\Controller\Result\Raw
     */
    function execute()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $orderId = $this->getRequest()->getParam('order_id');
        //echo $orderId;
        if ($orderId) {
            $response = false;
            $message = '';

            $model = $objectManager->create('Mangoit\MediaclipHub\Model\Orders');
            $mediaclipOrder = $model->getCollection();
            $mediaclipOrderData = $mediaclipOrder->addFieldToFilter('magento_order_id', array('eq' => $orderId));
            $mediaclipOrderData = $mediaclipOrderData->getData()[0];
            
            if ($mediaclipOrderData && $mediaclipOrderData['order_download_status'] == 0) {
                $model->setId($mediaclipOrderData['id']);
                $model->setOrderDownloadStatus(1);
                $model->save();
                $response = mc_h()->downloadAndUploadOrderFilesToServer($orderId);

            } else if($mediaclipOrderData && $mediaclipOrderData['order_download_status'] == 1){
                $message = 'Order already downloaded.';
            }

            if ($message == '') {
                if ($response) {
                    $message = 'Order downloaded successfully.';
                } else {
                    $model->setId($mediaclipOrderData['id']);
                    $model->setOrderDownloadStatus(0);
                    $model->save();
                    $message = 'Failed to download order.';
                }
            }
            echo json_encode(array('message' => $message));
            exit;
        }
    }
}