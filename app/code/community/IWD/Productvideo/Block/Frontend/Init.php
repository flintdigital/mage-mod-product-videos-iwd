<?php
class IWD_Productvideo_Block_Frontend_Init extends Mage_Core_Block_Template
{
    const XML_PATH_VIDEO_IN_POPUP = 'iwd_productvideo/video/in_popup';
    const XML_PATH_INTEGRATION_IMAGE_BOX = "iwd_productvideo/integration/image_box";
    const XML_PATH_INTEGRATION_THUMBNAILS_BOX = "iwd_productvideo/integration/thumbnails_box";
    const XML_PATH_INTEGRATION_THUMBNAILS_POSITION = "iwd_productvideo/integration/thumbnails_position";
    const XML_PATH_VIDEO_AS_FIRST_IMAGE = "iwd_productvideo/video/video_as_first_image";
    const XML_PATH_VIDEO_PRE_LOAD = "iwd_productvideo/video/preload_videos";

    private $collection = null;

    public function getUrlLoadVideo()
    {
        $_secure = Mage::app()->getStore()->isCurrentlySecure();
        $_store = Mage::app()->getStore()->getStoreId();
        return Mage::getUrl('iwd_productvideo/player/getvideo', array('_secure' => $_secure, '_store' => $_store));
    }

    public function getImageBox()
    {
        return Mage::getStoreConfig(self::XML_PATH_INTEGRATION_IMAGE_BOX, Mage::app()->getStore());
    }

    public function getIsPreLoadVideos()
    {
        return Mage::getStoreConfig(self::XML_PATH_VIDEO_PRE_LOAD, Mage::app()->getStore());
    }

    public function getThumbnailsBox()
    {
        return Mage::getStoreConfig(self::XML_PATH_INTEGRATION_THUMBNAILS_BOX, Mage::app()->getStore());
    }

    public function getShowInPopup()
    {
        return Mage::getStoreConfig(self::XML_PATH_VIDEO_IN_POPUP, Mage::app()->getStore()) ? 1 : 0 ;
    }

    public function getThumbnailsPosition()
    {
        return Mage::getStoreConfig(self::XML_PATH_INTEGRATION_THUMBNAILS_POSITION, Mage::app()->getStore());
    }

    public function getVideosCollection()
    {
        if($this->collection === null){
            $product_id = Mage::registry('current_product')->getId();
            $this->collection = Mage::getModel('iwd_productvideo/productvideo')->getVideoCollectionByProduct($product_id);
        }
        return $this->collection;
    }

    public function getImageUrl($video)
    {
        $image = $video->getImage();
        $width = 100;
        $height = 100;
        return Mage::helper('iwd_productvideo')->GetImageUrl($image, $width, $height);
    }

    public function getVideoIdAsFirstImage()
    {
        try{
            $as_first_image = Mage::getStoreConfig(self::XML_PATH_VIDEO_AS_FIRST_IMAGE, Mage::app()->getStore());
            if($as_first_image){
                $collection = $this->getVideosCollection();
                if(isset($collection[0])){
                    return $collection[0]->getImage();
                }
            }
        }catch (Exception $e){

        }

        return 0;
    }
}