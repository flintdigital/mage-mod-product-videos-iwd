<?php
class IWD_Productvideo_Block_Adminhtml_Productvideo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('iwd_productvideo');

        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addStoreFilter();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('iwd_productvideo')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
            'filter_index' => 'entity_id',
            'type' => 'number',
            'sortable' => true
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('iwd_productvideo')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'filter_index' => 'name',
            'width' => '200px',
            'sortable' => true
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('iwd_productvideo')->__('SKU'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'sku',
            'filter_index' => 'sku',
            'type' => 'text',
            'sortable' => true
        ));

        $this->addColumn('video', array(
            'header' => Mage::helper('iwd_productvideo')->__('Video(s)'),
            'align' => 'left',
            'index' => 'video',
            'type' => 'image',
            'height' => '75px',
            'filter' => false,
            'sortable' => false,
            'column_css_class' => 'sortable_video',
            'renderer' => 'iwd_productvideo/adminhtml_productvideo_renderer_video',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('iwd_productvideo')->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'width' => '80px',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'iwd_productvideo/adminhtml_productvideo_renderer_action',
        ));

        return parent::_prepareColumns();
    }


    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }


}