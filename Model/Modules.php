<?php
namespace Mangoit\MediaclipHub\Model;
use Mangoit\MediaclipHub\Model\ResourceModel\Modules as R;
// 2019-04-11
class Modules extends \Magento\Framework\Model\AbstractModel {
    /** 2019-04-11 */
    function _construct() {$this->_init(R::class);}
    function getMediaClipModuleName($_modulecode){
        $collection = $this->getCollection()->addFieldToFilter('module_code', $_modulecode)->getData();
        if (empty($collection)) {
            return false;
        }
        $response = $collection[0]['module_name'];
        return $response;
    }
}
