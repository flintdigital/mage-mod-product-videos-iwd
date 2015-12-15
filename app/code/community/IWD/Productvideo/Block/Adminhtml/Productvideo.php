<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_productvideo';
        $this->_controller = 'adminhtml_productvideo';
        $this->_headerText = Mage::helper('iwd_productvideo')->__('Product Manager');

        parent::__construct();
        $this->_removeButton('add');
    }
}