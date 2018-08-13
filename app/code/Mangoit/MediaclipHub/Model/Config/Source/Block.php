<?php
namespace Mangoit\MediaclipHub\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Block implements ArrayInterface
{

    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Magento\Cms\Model\Block')->getCollection();
        
        $staticBlock = [0 => '-- Select Static Block --'];
        if ($model) {
            foreach ($model as $block) {
                $staticBlock[$block->getIdentifier()] = $block->getTitle();
            }
        }
            $maxvalList = $staticBlock;
        return $maxvalList;
    }
}
