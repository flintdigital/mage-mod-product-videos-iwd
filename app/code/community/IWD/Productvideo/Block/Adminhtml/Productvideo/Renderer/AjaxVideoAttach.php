<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo_Renderer_AjaxVideoAttach extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public static $_attachedToProduct;
    public function render(Varien_Object $row)
    {
        $productId = $this->getRequest()->getParam('id');
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/iwd_productvideo_video/attachVideoToProduct');
        $id = $row->getVideoId();
        $render = '<input
            type="checkbox"
            class="ajax-video-attach"
            id="video-attach-' . $id . '"
            data-url="' . $url . '"
            data-product-id="' . $productId . '"
            value="' . $id . '"';
        if(in_array($id, $this->_getAttachedVideosToProduct())){
            $render .= 'checked="checked"';
        }
        $render .= '/>';
        echo $render;
    }

    protected function _getAttachedVideosToProduct()
    {
        if(!empty(self::$_attachedToProduct)){
            return self::$_attachedToProduct;
        }

        $productId = $this->getRequest()->getParam('id');
        $videos = Mage::getModel('iwd_productvideo/productvideo')
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->getColumnValues('video_id');

        self::$_attachedToProduct = $videos;
        return self::$_attachedToProduct;
    }
}