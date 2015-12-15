<?php
class IWD_Productvideo_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    private $parent;

    protected function _prepareLayout()
    {
        $this->parent = parent::_prepareLayout();

        if(Mage::app()->getRequest()->getParam('id')!==null){
            $this->addTab('productvideo', array(
                'label'     => Mage::helper('catalog')->__('Product Videos'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('adminhtml/iwd_productvideo_productvideo/videogrid', array('_current' => true)),
            ));
        }
        return $this->parent;
    }
}