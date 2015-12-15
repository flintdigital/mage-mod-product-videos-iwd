<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('iwd_productvideo');
        if ($this->_countLinkedProductsToVideo() > 0)
            $this->setDefaultFilter(array('video_link' => 1));
        $this->setUseAjax(true);

    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'video_link') {
            $productIds = $this->_getLinkedProductsToVideo();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('video_link', array(
            'header_css_class' => 'a-center',
            'column_css_class' => 'video_product_link_checkbox',
            'type' => 'checkbox',
            'width' => '60px',
            'name' => 'video_link',
            'align' => 'center',
            'index' => 'entity_id',
            'renderer' => 'IWD_Productvideo_Block_Adminhtml_Productvideo_Renderer_AjaxProductAttach',
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('iwd_productvideo')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'filter_index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));

        $this->addColumn('name', array(
            'header' => Mage::helper('iwd_productvideo')->__('Product Name'),
            'width' => '150px',
            'index' => 'name'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('iwd_productvideo')->__('SKU'),
            'width' => '80px',
            'index' => 'sku'
        ));
        $this->addColumn('video', array(
            'header' => Mage::helper('iwd_productvideo')->__('Video(s)'),
            'align' => 'left',
            'index' => 'video',
            'type' => 'image',
            'filter_index' => 'video',
            'filter' => false,
            'sortable' => false,
            'column_css_class' => 'sortable_video',
            'renderer' => 'iwd_productvideo/adminhtml_productvideo_renderer_video',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid_pv', array('_current' => true));
    }

    public function getRowClickCallback()
    {
        return 'IWD.PV.Admin.rowClick';
    }

    protected function _countLinkedProductsToVideo()
    {
        $videoId = $this->getRequest()->getParam('video_id');
        return Mage::getModel('iwd_productvideo/productvideo')
            ->getCollection()
            ->addFieldToFilter('video_id', $videoId)
            ->count();
    }

    protected function _getLinkedProductsToVideo()
    {
        $videoId = $this->getRequest()->getParam('video_id');

        $collection = Mage::getModel('iwd_productvideo/productvideo')
            ->getCollection()
            ->addFieldToFilter('video_id', $videoId);

        $products = array();
        foreach ($collection as $item)
            $products[] = $item->getProductId();

        return $products;
    }

}