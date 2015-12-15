<?php
class IWD_Productvideo_Block_Frontend_Player extends Mage_Catalog_Block_Product_Abstract
{
    const XML_PATH_AUTOPLAY_VIDEO = "iwd_productvideo/video/autoplay";
    const XML_PATH_YOUTUBE_REL = "iwd_productvideo/video/youtube_rel";

    public function isAutoplayVideo()
    {
        return Mage::getStoreConfig(self::XML_PATH_AUTOPLAY_VIDEO, Mage::app()->getStore()) ? 1 : 0;
    }

    public function notShowRelatedVideo()
    {
        return Mage::getStoreConfig(self::XML_PATH_YOUTUBE_REL, Mage::app()->getStore()) ? 0 : 1;
    }
}