<?php
class IWD_Productvideo_Block_Adminhtml_Video extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_productvideo';
        $this->_controller = 'adminhtml_video';

        $this->_headerText = Mage::helper('iwd_productvideo')->__('Video Manager');
        $this->_addButtonLabel = Mage::helper('iwd_productvideo')->__('Add New Video');

        parent::__construct();
    }
}