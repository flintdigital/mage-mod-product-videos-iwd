<?php
class IWD_Productvideo_Block_Frontend_Media extends Mage_Catalog_Block_Product_View_Media
{
    protected  $_all = null;
    public function getGalleryImages()
    {
        if($this->_all === null) {
            $collection = parent::getGalleryImages();
            if (empty($collection) || $collection->getSize() == 0) {
                $productMediaConfig = Mage::getModel('catalog/product_media_config');
                $image = $this->getProduct()->getImage();

                if ($image != 'no_selection') {
                    $img['url'] = $productMediaConfig->getMediaUrl($image);
                    $img['id'] = null;
                    $img['path'] = $productMediaConfig->getMediaPath($image);
                    $collection = new Varien_Data_Collection();
                    $collection->addItem(new Varien_Object($img));
                }
            }

            $videosInit = new IWD_Productvideo_Block_Frontend_Init();
            $this->_all = new Varien_Data_Collection();
            if($videosInit->getThumbnailsPosition() == 'before')
                $this->_completeCollectionWithVideo();
            foreach($collection as $item)
                $this->_all->addItem($item);
            if($videosInit->getThumbnailsPosition() == 'after')
                $this->_completeCollectionWithVideo();
        }

        return $this->_all;
    }

    private function _completeCollectionWithVideo()
    {
        if(Mage::getStoreConfig('iwd_productvideo/general/enabled')) {
            $videosInit = new IWD_Productvideo_Block_Frontend_Init();
            $videos = $videosInit->getVideosCollection();
            if(!empty($videos)) {
                foreach($videos as $video) {
                    $img = new Varien_Object();
                    $img->setData('path', Mage::helper('iwd_productvideo')->GetMediaImagePath($video->getImage()));
                    $img->setData('file', $videosInit->getImageUrl($video));
                    $img->setData('url', Mage::helper('iwd_productvideo')->GetMediaImageRelativePath() . $video->getImage());
                    $img->setData('label', $video->getTitle());
                    $this->_all->addItem($img);
                }
            }
        }
    }
}
