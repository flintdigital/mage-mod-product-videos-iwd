<?php
class IWD_Productvideo_Model_System_Config_Sort
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'before', 'label' => 'Before  (Video - Image)'),
            array('value' => 'after', 'label' => 'After (Image - Video)'),
        );
    }
}