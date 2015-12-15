<?php
class IWD_Productvideo_Model_Productvideo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_productvideo/productvideo');
    }

    public function getProductCollectionByVideo($video_id)
    {
        $productCollection = array();
        $collection = Mage::getModel('iwd_productvideo/productvideo')
            ->getCollection()
            ->addFieldToFilter('video_id', $video_id);

        foreach ($collection as $item) {
            $productCollection[] = Mage::getModel('catalog/product')->load($item->getProductId());
        }
        return $productCollection;
    }

    public function getAllVideoCollectionByProduct($product_id)
    {
        $iwdVideoTableName = Mage::getSingleton('core/resource')->getTableName('iwd_video');

        $videoCollection = Mage::getModel('iwd_productvideo/productvideo')
            ->getCollection()
            ->addFieldToFilter('product_id', $product_id);
        $videoCollection->getSelect()
            ->join(array('iwd_video' => $iwdVideoTableName),
                '`iwd_video`.`video_id` = `main_table`.`video_id`',
                array('*'))
            ->order('video_position');

        return $videoCollection;
    }

    public function getVideoCollectionByProduct($product_id)
    {
        $iwdVideoTableName = Mage::getSingleton('core/resource')->getTableName('iwd_video');

        $collection = $this->getCollection()
            ->addFieldToFilter('product_id', $product_id);
        $collection->getSelect()
            ->joinLeft(array('iwd_video' => $iwdVideoTableName),
                '`iwd_video`.`video_id` = `main_table`.`video_id`',
                array('*'))
            ->where('video_status = ?', 1)
            ->order('video_position');

        $collectionStoreView = null;
        $currentStoreView = Mage::app()->getStore()->getStoreId();
        foreach ($collection as $item) {
            $allowStoreViews = unserialize($item->getVideoStoreView());
            if (in_array(0, $allowStoreViews) || in_array($currentStoreView, $allowStoreViews))
                $collectionStoreView[] = $item;
        }

        return $collectionStoreView;
    }

    public function updateVideoPositionByProduct($productID, $videoIDs)
    {
        try {
            $position = 1;
            foreach ($videoIDs as $videoID) {
                $collection = $this->getCollection()
                    ->addFieldToFilter('video_id', $videoID)
                    ->addFieldToFilter('product_id', $productID);

                foreach ($collection as $item) //must by one element
                {
                    $item->setVideoPosition($position++);
                    $item->save();
                }
            }
        } catch (Exception $e) {
            return 0;
        }
        return 1;
    }

    public function SaveProductVideoLinks($products, $video_id)
    {
        $saveProducts = array();
        $collectionVideo = $this->getCollection()->addFieldToFilter('video_id', $video_id);
        foreach ($collectionVideo as $item)
            $saveProducts[] = $item->getProductId();

        foreach ($products as $product_id) {
            if (!is_numeric($product_id) || in_array($product_id, $saveProducts)) {
                if (($key = array_search($product_id, $saveProducts)) !== FALSE)
                    unset($saveProducts[$key]);
                continue;
            }

            $productVideoData = array(
                'product_id' => $product_id,
                'video_id' => $video_id,
                'video_position' => $this->getCollection()->addFieldToFilter('product_id', $product_id)->count() + 1,
            );

            $this->setData($productVideoData)->save();
        }

        foreach ($saveProducts as $id) {
            $collection = $this->getCollection()
                ->addFieldToFilter('product_id', $id)
                ->addFieldToFilter('video_id', $video_id);
            foreach ($collection as $item) //must by one element
                $item->delete();
        }
    }
}