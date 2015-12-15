<?php
/**
 * Created by PhpStorm.
 * User: Kate
 * Date: 29.06.15
 * Time: 17:25
 */
class IWD_Productvideo_Helper_CatalogImage extends Mage_Catalog_Helper_Image
{
    public function resize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        if(strpos($this->getImageFile(), 'iwd_video') !== false)
            return $this->getImageFile();
        return $this;
    }
}