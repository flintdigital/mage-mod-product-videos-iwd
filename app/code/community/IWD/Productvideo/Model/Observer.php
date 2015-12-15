<?php
class IWD_Productvideo_Model_Observer
{
    public function checkRequiredModules($observer)
    {
        $cache = Mage::app()->getCache();

        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                if ($cache->load("iwd_product_video") === false) {
                    $message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Product Video</strong>  installation.<br />
					Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and set it up via Magento Connect.<br />
					Please refer link to <a href="https://docs.google.com/document/d/18ZyCiEzchBYnus6xsSDqa8aR0mZY1h2F3zMqK77vSTI/edit" target="_blank">installation guide</a>';

                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_product_video', array("iwd_product_video"), $lifeTime = 5);
                }
            }
        }
    }
}
