<?php
class IWD_Productvideo_Model_Mysql4_Productvideo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_productvideo/productvideo');
    }
}