<?php
class IWD_Productvideo_Block_Adminhtml_Video_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $width = 75;
        $height = 55;
        $image_title = $row['image'];
        $image = Mage::helper('iwd_productvideo')->GetMediaImagePath($image_title);

        if (empty($image_title) || !file_exists($image)){
            $image_title = Mage::helper('iwd_productvideo/image')->LoadExternImage($row['video_id'], $row['video_type'], $row['url']);
        }

        $image = Mage::helper('iwd_productvideo/image')->getImageResize($image_title, $width, $height);
        return '<img width="' . $width . 'px" height="' . $height . 'px" src="' . $image . '" />';
    }

}
