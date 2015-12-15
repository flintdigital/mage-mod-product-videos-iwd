<?php
class IWD_Productvideo_Block_Adminhtml_Video_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    const PLAYER_WIDTH = 280;
    const PLAYER_HEIGHT = 200;

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $edit = false;
        if (Mage::registry('video_data'))
            if (Mage::registry('video_data')->getVideoId())
                $edit = true;
        $fieldsetVideoType = $form->addFieldset(
            'productvideo_form_video_type',
            array('legend' => Mage::helper('iwd_productvideo')->__('Video Type'))
        );

        $videoTypeArray = array(
            'label' => Mage::helper('iwd_productvideo')->__('Type'),
            'required' => true,
            'name' => 'video_type',
            'values' => array('youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'local' => 'From local PC'),
        );
        if ($edit)
            $videoTypeArray['disabled'] = true;
        $fieldsetVideoType->addField('video_type', 'select', $videoTypeArray);

        $fieldsetVideoType->addField('youtube_url', 'text', array(
            'label' => Mage::helper('iwd_productvideo')->__('Youtube Video Code'),
            'class' => 'required-entry iwd_video_url',
            'required' => true,
            'name' => 'url',
            'after_element_html' => '<p class="note"><span>http://www.youtube.com/watch?v=<b>XXXXXXXXXX</b></span></p>',
        ));
        $fieldsetVideoType->addField('vimeo_url', 'text', array(
            'label' => Mage::helper('iwd_productvideo')->__('Vimeo Video Code'),
            'class' => 'required-entry iwd_video_url',
            'required' => true,
            'name' => 'url',
            'after_element_html' => '<p class="note"><span>http://vimeo.com/<b>XXXXXXXX</b></span></p>',
        ));

        $fieldsetVideoType->addField('local_url', 'file', array(
            'label' => Mage::helper('iwd_productvideo')->__('Upload video from PC'),
            'class' => 'required-entry iwd_video_url',
            'required' => true,
            'name' => 'url',
            'after_element_html' => '<p class="note"><span>Supported formats: mp4, ogv, ogg, webm</br>
                                    Max file size: ' . Mage::helper('iwd_productvideo')->getMaxUploadFileSize() . '</span></p>',
        ));

        if ($edit) {
            $fieldsetVideoType->addType('video', 'IWD_Productvideo_Varien_Data_Form_Element_Video');
            $fieldsetVideoType->addField('video', 'video', array(
                'embed_code' => $this->getEmbedVideoCode(),
            ));
        }

        $fieldset = $form->addFieldset('productvideo_form_video_info', array('legend' => Mage::helper('iwd_productvideo')->__('Video information')));

        if ($edit)
            $fieldset->addField('video_id', 'hidden', array(
                'name' => 'video_id',
            ));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('iwd_productvideo')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('iwd_productvideo')->__('Short video description'),
            'name' => 'description',
        ));

        $fieldset->addField('video_status', 'select', array(
            'label' => Mage::helper('iwd_productvideo')->__('Status'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'video_status',
            'values' => array('1' => 'Enabled', '0' => 'Disabled'),
            'value' => '1',
        ));

        $fieldset->addType('thumbnail', 'IWD_Productvideo_Varien_Data_Form_Element_Thumbnail');
        $fieldset->addField('image', 'thumbnail', array(
            'label' => Mage::helper('iwd_productvideo')->__('Image'),
            'name' => 'image',
            'required' => true,
            'after_element_html' => '<p class="note"><span id="image_note"></span></p>',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('video_store_view', 'multiselect', array(
                'name' => 'video_store_view',
                'label' => Mage::helper('iwd_productvideo')->__('Store View'),
                'title' => Mage::helper('iwd_productvideo')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value' => '0',
            ));
        } else {
            $fieldset->addField('video_store_view_single', 'hidden', array(
                'name' => 'video_store_view_single',
                'value' => '1',
            ));
        }

        try {
            if (Mage::getSingleton('adminhtml/session')->getVideoData()) {
                $form->setValues(Mage::getSingleton('adminhtml/session')->getVideoData());
                Mage::getSingleton('adminhtml/session')->setVideoData(null);
            } elseif (Mage::registry('video_data')) {
                $data = Mage::registry('video_data')->getData();

                Mage::helper('iwd_productvideo/image')->getImageResize($data['image'], 75, 75);
                $data['image'] = Mage::helper('iwd_productvideo')->GetMediaImageUrl($data['image']);

                if ($data['video_type'] == 'local')
                    $data['local_url'] = Mage::helper('iwd_productvideo')->GetVideoUrl($data['url']);
                else
                    $data[$data['video_type'] . '_url'] = $data['url'];

                if (!Mage::app()->isSingleStoreMode())
                    $data['video_store_view'] = unserialize($data['video_store_view']);
                else
                    $data['video_store_view_single'] = 1;

                $form->setValues($data);
            }
        } catch (Exception $e) {
            Mage::log(__CLASS__ . ": " . $e->getMessage());
        }

        return parent::_prepareForm();
    }

    protected function getEmbedVideoCode()
    {
        if ($data = Mage::registry('video_data')->getData()){
            if (isset($data['video_type'])) {
                if ($data['video_type'] == 'local')
                    return $this->getLocalVideoCode($data);
                if ($data['video_type'] == 'youtube')
                    return $this->getYoutubeVideoCode($data);
                if ($data['video_type'] == 'vimeo')
                    return $this->getVimeoVideoCode($data);
            }
        }
        return '';
    }

    protected function getLocalVideoCode($data)
    {
        return '<video class="local-video-player video-js vjs-default-skin" controls preload="none"
                   width="' . self::PLAYER_WIDTH . 'px" height="' . self::PLAYER_HEIGHT . 'px"
                   poster="' . Mage::helper('iwd_productvideo')->GetMediaImageUrl($data['image']) . '"
                   data-setup="{}">' .
        Mage::helper('iwd_productvideo')->GetVideoSource($data) .
        '</video>';
    }

    protected function getYoutubeVideoCode($data)
    {
        $protocol = Mage::app()->getStore()->isCurrentlySecure() ? "https:" : "http:";

        return '<iframe class="youtube-video-player"
                    src="'. $protocol .'//www.youtube.com/embed/' . $data['url'] . '"
                    width="' . self::PLAYER_WIDTH . 'px" height="' . self::PLAYER_HEIGHT . 'px" frameborder="0" webkitallowfullscreen mozallowfullscreen
                    allowfullscreen>
                </iframe>';
    }

    protected function getVimeoVideoCode($data)
    {
        $protocol = Mage::app()->getStore()->isCurrentlySecure() ? "https:" : "http:";

        return '<iframe class="vimeo-video-player" src="'. $protocol .'//player.vimeo.com/video/' . $data['url'] . '"
                    width="' . self::PLAYER_WIDTH . 'px" height="' . self::PLAYER_HEIGHT . 'px" frameborder="0" webkitallowfullscreen mozallowfullscreen
                    allowfullscreen>
                </iframe>';
    }
}