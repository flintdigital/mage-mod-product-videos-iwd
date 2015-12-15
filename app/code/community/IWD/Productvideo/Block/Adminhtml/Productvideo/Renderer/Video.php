<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo_Renderer_Video extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $videoCollection = Mage::getModel('iwd_productvideo/productvideo')->getAllVideoCollectionByProduct($row['entity_id']);
        $videoBlock = '';
        $currentVideoId = $this->getRequest()->getParam('video_id');
        $storeId = $this->getRequest()->getParam('store');
        foreach ($videoCollection as $video) {
            $stores = unserialize($video->getVideoStoreView());
            if ($storeId !== null && !in_array($storeId, $stores) && !in_array(0, $stores)){
                continue;
            }

            $videoBlock .= $this->_getImageBlock($video, $currentVideoId);
        }

        return $videoBlock;
    }

    protected function _getImageSrc($video)
    {
        $image_title = $video->getImage();
        $image = Mage::helper('iwd_productvideo')->GetMediaImagePath($image_title);

        if (empty($image_title) || !file_exists($image)){
            $image_title = Mage::helper('iwd_productvideo/image')
                ->LoadExternImage($video->getVideoId(), $video->getVideoType(), $video->getUrl());
        }

        return Mage::helper('iwd_productvideo/image')->getImageResize($image_title, 75, 55);
    }

    protected function _getImageBlock($video, $currentVideoId)
    {
        $imageSrc = $this->_getImageSrc($video);

        $videoBlock = '<div class="video-launcher" id="'.$video->getVideoId() . '_' . $video->getProductId() .'">'
            .'<i class="play-button fa fa-2x fa-caret-right" data-video-id="'.$video->getVideoId().'"></i>';
            /*.'<input type="checkbox" data-video-id="'.$video->getVideoId().'" class="video_as_first_image" title="Us video as the first image"/>';*/
            $videoBlock .= '<img id="' . $video->getVideoId() . '_' . $video->getProductId() .
                '" src="' . $imageSrc . '" class="video-semblance-edit ';

            $videoBlock .= ($video->getVideoStatus() == 0) ? 'video-enabled ' : '';

            $videoBlock .= ($video->getVideoId() == $currentVideoId) ?
                'video-current" title="It\'s current video"/>' :
                '" title="Video: ' . $video->getTitle() . '"/>';

        return $videoBlock . '</div>';
    }
}
