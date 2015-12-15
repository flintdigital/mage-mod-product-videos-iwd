<?php
class IWD_Productvideo_Block_Adminhtml_Video_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row['video_status'] === '1') return 'Enabled';
        if ($row['video_status'] === '0') return 'Disabled';
        return '';
    }
}
