<?php
namespace Mangoit\MediaclipHub\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class postDispatch implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_replenish;
    protected $order;
    protected $_registry;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    function __construct(
        \Mangoit\MediaclipHub\Model\OrdersFactory $replenish,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order $order
    ) {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_replenish = $replenish;
        $this->order = $order;
    }
 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {        
        $addNewOption = true;
        if ($this->_registry->registry('current_product')) {
            
            $_product = $this->_registry->registry('current_product')->debug();
            if ($_product['attribute_set_id'] != 4) {
                
                $productOptions = $_product['options'];
                if ($productOptions) {
                    foreach($productOptions as $optionDetail){
                        
                        if ($optionDetail['default_title'] == 'Project') {
                            $addNewOption = false;
                        }
                    }
                } else {
                    $addNewOption = true;
                }

                if ($addNewOption) {

                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($_product['entity_id']);
                    $optionData = array(
                        'title'             => 'Project',
                        'type'              => 'field',
                        'price'             => '0.00',
                        'price_type'        => 'fixed',
                        'is_require'        => false,
                        'sort_order'        => 0,
                    );
                    $product->setHasOptions(1);
                    $product->getResource()->save($product);
                    $option = $this->_objectManager->create('\Magento\Catalog\Model\Product\Option')
                            ->setProductId($_product['entity_id'])
                            ->setStoreId($product->getStoreId())
                            ->addData($optionData);
                    $option->save();
                    $product->addOption($option);
                }
            }
        }
        
    }
}