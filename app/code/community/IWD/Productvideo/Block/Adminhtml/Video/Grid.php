<?php
class IWD_Productvideo_Block_Adminhtml_Video_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('videoGrid');

        $this->_blockGroup = 'iwd_productvideo';
        $this->_controller = 'adminhtml_video';

        $this->setDefaultSort('video_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_productvideo/video')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('video_id', array(
            'header' => Mage::helper('iwd_productvideo')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'video_id',
            'filter_index' => 'video_id',
            'type' => 'number',
            'sortable' => true
        ));

        $this->addColumn('image', array(
            'header' => Mage::helper('iwd_productvideo')->__('Image'),
            'align' => 'left',
            'index' => 'image',
            'type' => 'image',
            'filter_index' => 'image',
            'width' => '75px',
            'height' => '75px',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'iwd_productvideo/adminhtml_video_renderer_image',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('iwd_productvideo')->__('Title'),
            'align' => 'left',
            'index' => 'title',
            'width' => '200px',
            'filter_index' => 'title',
            'sortable' => true
        ));

        $this->addColumn('description', array(
            'header' => Mage::helper('iwd_productvideo')->__('Description'),
            'align' => 'left',
            'index' => 'description',
            'filter_index' => 'description',
            'type' => 'text',
            'sortable' => true
        ));

        $this->addColumn('products', array(
            'header' => Mage::helper('iwd_productvideo')->__('Product(s) sku'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'products',
            'filter_index' => 'products',
            'sortable' => false,
            'filter_condition_callback' => array($this, '_customProductFilter'),
            'renderer' => 'iwd_productvideo/adminhtml_video_renderer_products',
        ));

        $this->addColumn('video_store_view', array(
            'header' => Mage::helper('iwd_productvideo')->__('Store View'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'video_store_view',
            'filter_index' => 'video_store_view',
            'type' => 'options',
            'sortable' => false,
            'renderer' => 'iwd_productvideo/adminhtml_video_renderer_store',
            'filter_condition_callback' => array($this, '_customStoreViewFilter'),
            'options' => Mage::getSingleton('iwd_productvideo/video')->getStoreViewOptionArray(),
        ));

        $this->addColumn('video_status', array(
            'header' => Mage::helper('iwd_productvideo')->__('Status'),
            'align' => 'left',
            'width' => '75px',
            'index' => 'video_status',
            'filter_index' => 'video_status',
            'type' => 'options',
            'sortable' => true,
            'renderer' => 'iwd_productvideo/adminhtml_video_renderer_status',
            'options' => Mage::getSingleton('iwd_productvideo/video')->getStatusOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('video_id');
        $this->getMassactionBlock()->setFormFieldName('video');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('iwd_productvideo')->__('Delete video'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('iwd_productvideo')->__('Are you sure?')
        ));


        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => Mage::getSingleton('iwd_productvideo/video')->getStatusOptionArray()
                )
            )
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('video_id' => $row->getVideoId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _customProductFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value === null)
            return null;

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

    protected function _customStoreViewFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value === null)
            return null;

        $videoCollection = Mage::getModel('iwd_productvideo/video')->getCollection();

        $videoIds = array();
        foreach ($videoCollection as $video) {
            $store_views = unserialize($video->getVideoStoreView());
            if (in_array($value, $store_views) && !in_array(0, $store_views))
                $videoIds[$video->getVideoId()] = $video->getVideoId();
            if ($value==0 && in_array(0, $store_views))
                $videoIds[$video->getVideoId()] = $video->getVideoId();
        }
        return $this->getCollection()->addFieldToFilter('video_id', array('in' => $videoIds))->load();
    }
}