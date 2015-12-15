<?php
class IWD_Productvideo_Block_Adminhtml_Video_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $products = Mage::getModel('iwd_productvideo/productvideo')->getProductCollectionByVideo($row['video_id']);

        $prod = array();
        foreach ($products as $item)
            $prod[] = $item->getSku();

        return implode(', ', $prod);
    }
}
