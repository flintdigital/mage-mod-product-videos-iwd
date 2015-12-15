<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo_Renderer_AjaxProductAttach extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public static $_relatedToVideo;
    public function render(Varien_Object $row)
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/iwd_productvideo_video/attachVideoToProduct');
        $id = $row->getId();
        $render = '<input
            type="checkbox"
            class="ajax-product-attach"
            id="product-attach-' . $id . '"
            data-url="' . $url . '"
            value="' . $id . '"';
        if(in_array($id, $this->_getLinkedProductsToVideo()))
            $render .= 'checked="checked"';
        $render .= '/>';
        echo $render;
    }

    protected function _getLinkedProductsToVideo()
    {
        if(!empty(self::$_relatedToVideo))
            return self::$_relatedToVideo;

        $videoId = $this->getRequest()->getParam('video_id');

        $collection = Mage::getModel('iwd_productvideo/productvideo')
            ->getCollection()
            ->addFieldToFilter('video_id', $videoId);

        $products = array();
        foreach ($collection as $item)
            $products[] = $item->getProductId();

        self::$_relatedToVideo = $products;
        return self::$_relatedToVideo;
    }
}