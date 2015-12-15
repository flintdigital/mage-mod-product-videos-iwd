<?php
class IWD_Productvideo_Varien_Data_Form_Element_Video extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function getElementHtml()
    {
        $html = '';
        if ($this->getEmbedCode())
            $html = '<div>'.$this->getEmbedCode().'</div>';

        return $html;
    }

    public function getName()
    {
        return $this->getData('name');
    }
}