<?php
class IWD_Productvideo_Block_Adminhtml_Video_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'video_id';
        $this->_blockGroup = 'iwd_productvideo';
        $this->_controller = 'adminhtml_video';

        $this->_updateButton('save', 'label', Mage::helper('iwd_productvideo')->__('Save Video'));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
            'class' => 'save',
        ), -100, 100);

        if (Mage::registry('video_data') && Mage::registry('video_data')->getVideoId())
            $this->_addButton('add', array(
                'label' => Mage::helper('adminhtml')->__('Delete video'),
                'class' => 'delete',
                'onclick' =>
                    'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?') . '\', \''
                    . $this->getUrl('*/*/delete', array('video' => Mage::registry('video_data')->getVideoId())) . '\')',
            ), -1, 11);

    }

    public function getHeaderText()
    {
        if (Mage::registry('video_data') && Mage::registry('video_data')->getVideoId())
            return Mage::helper('iwd_productvideo')->__("Edit Video '%s'", Mage::registry('video_data')->getTitle());

        return Mage::helper('iwd_productvideo')->__('Add New Video');
    }
}