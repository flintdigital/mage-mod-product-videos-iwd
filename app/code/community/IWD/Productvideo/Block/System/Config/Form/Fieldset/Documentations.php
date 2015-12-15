<?php
class IWD_Productvideo_Block_System_Config_Form_Fieldset_Documentations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="https://docs.google.com/document/d/1EhN_zKt7zGeE25ZzMq7zq6Qr5b9fkPvdwoEFhmDyEtw/" target="_blank">User Guide</span>';
    }
}