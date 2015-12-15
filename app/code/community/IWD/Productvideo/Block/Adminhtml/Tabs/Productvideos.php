<?php
class IWD_Productvideo_Block_Adminhtml_Tabs_Productvideos extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('attached_videos');
        $this->setDefaultSort('video_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('iwd_productvideo/video')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('attached_videos', array(
            'type'      => 'checkbox',
            'name'      => 'attached_videos',
            'values'    => $this->_getAttachedVideos(),
            'align'     => 'center',
            'index'     => 'video_id',
            'width' => '60px',
            'renderer'  => 'IWD_Productvideo_Block_Adminhtml_Productvideo_Renderer_AjaxVideoAttach',
        ));

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

        $this->addColumn('edit_action',
            array(
                'header'    =>  Mage::helper('iwd_productvideo')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getVideoId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('iwd_productvideo')->__('Edit'),
                        'url'       => array('base'=> 'iwd_productvideo/video/edit'),
                        'field'     => 'video_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'attached_videos') {
            $videoIds = $this->_getAttachedVideos();
            if (empty($videoIds)) {
                $videoIds = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('video_id', array('in' => $videoIds));
            } elseif (!empty($videoIds)) {
                $this->getCollection()->addFieldToFilter('video_id', array('nin' => $videoIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    private function _getAttachedVideos()
    {
        $productId = $this->getRequest()->getParam('id');
        return Mage::getModel('iwd_productvideo/productvideo')->getCollection()
            ->addFieldToFilter('product_id', array('eq'=>$productId))
            ->getColumnValues('video_id');
    }

    public function getRowUrl($item)
    {
        return '';
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/videogrid', array('_current' => true));
    }
}