<?php
class IWD_Productvideo_Block_Adminhtml_Video_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $store_views = unserialize($row['video_store_view']);

        if (in_array(0, $store_views))
            return Mage::helper('iwd_productvideo')->__("All Store Views");

        foreach($store_views as $item)
            $stores[] = Mage::getSingleton('adminhtml/system_store')->getStoreName($item);

        return implode(', ', $stores);
    }
}
