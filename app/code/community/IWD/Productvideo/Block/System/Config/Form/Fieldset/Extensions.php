<?php
class IWD_Productvideo_Block_System_Config_Form_Fieldset_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $available = Mage::helper('iwd_productvideo')->isAvailableVersion();
        $version = Mage::getConfig()->getModuleConfig("IWD_Productvideo")->version;

        if ($available){
            return '<span class="notice">' . $version . '</span>';
        }else{
            return '<span class="error">' . $version . '<br />This module is available for Magento CE only.<br />You are using Enterprise version of Magento.
					<br />Please obtain Enterprise copy of the module at <a href="www.iwdextensions.com" target="_blank">www.iwdextensions.com</a></span>';
        }
    }
}