<?php
class IWD_Productvideo_Model_Mysql4_ProductVideo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('iwd_productvideo/product_video', 'entity_id');
    }
}