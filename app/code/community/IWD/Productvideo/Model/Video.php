<?php
class IWD_Productvideo_Model_Video extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_productvideo/video');
    }

    public function getStatusOptionArray()
    {
        return array(
            '1' => 'Enabled',
            '0' => 'Disabled'
        );
    }

    public function getStoreViewOptionArray()
    {
        $arr = array('0' => 'All Store Views');

        $store = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
        foreach ($store as $item)
            $arr[$item['store_id']] = $item['name'];

        return $arr;
    }

    public function VideoProductFilter($value)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('sku');
        $collection->getSelect()->where("sku like ?", "%$value%");

        $videoIds = array();
        foreach ($collection as $prod) {
            $videoCollection = Mage::getModel('iwd_productvideo/productvideo')->getAllVideoCollectionByProduct($prod->getEntityId());
            foreach ($videoCollection as $video)
                $videoIds[$video->getVideoId()] = $video->getVideoId();
        }

        return $this->getCollection()->addFieldToFilter('video_id', array('in' => $videoIds))->load();
    }
}