<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $imageEditUrl = $this->getUrl('adminhtml/catalog_product/edit', array(
                'store'=>$this->getRequest()->getParam('store'),
                'id'=>$row->getId())
        );

        $videoEditUrl  = $this->getUrl('adminhtml/iwd_productvideo_video/new');

        $html = '<a href="'.$imageEditUrl.'" target="_blank">' . Mage::helper('iwd_productvideo')->__("Add images") . '</a><br>' .
            '<a href="'.$videoEditUrl.'" target="_blank">' . Mage::helper('iwd_productvideo')->__("Add videos") . '</a>';
        return $html;
    }
}
