<?php
class IWD_Productvideo_Varien_Data_Form_Element_Thumbnail extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

    public function getElementHtml()
    {
        $html = '';
        if ($this->getValue())
            $html = '<img id="' . $this->getHtmlId() . '_image" style="border: 1px solid #d6d6d6;" title="' . $this->getValue() . '" src="' . $this->getValue() . '" alt="' . $this->getValue() . '" width="100">';

        $this->setClass('input-file');
        if ($this->getRequired())
            $this->addClass('required-entry');

        $html .= parent::getElementHtml();
        return $html;
    }

    protected function _getUrl()
    {
        return $this->getValue();
    }

    public function getName()
    {
        return $this->getData('name');
    }
}