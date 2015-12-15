<?php
class IWD_Productvideo_Block_Adminhtml_Video_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productvideo_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('iwd_productvideo')->__('Video Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section_video_info', array(
            'label' => Mage::helper('iwd_productvideo')->__('Video Information'),
            'title' => Mage::helper('iwd_productvideo')->__('Video Information'),
            'content' => $this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit_tab_form')->toHtml(),
        ));

        if (Mage::registry('video_data') && Mage::registry('video_data')->getVideoId())
        {
            $product_content = $this->getLayout()->createBlock('iwd_productvideo/adminhtml_productvideo_edit_tab_form', 'iwd_productvideo.grid')->toHtml();
            $serialize_block = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
            $serialize_block->initSerializerBlock('iwd_productvideo.grid', 'getSelectedProducts', 'products', 'selected_products');
            $product_content .= $serialize_block->toHtml();
            $this->addTab('form_section_product_video', array(
                'label' => Mage::helper('iwd_productvideo')->__('Product Video'),
                'title' => Mage::helper('iwd_productvideo')->__('Product Video'),
                'content' => $product_content
            ));

        }

        return parent::_beforeToHtml();
    }
}