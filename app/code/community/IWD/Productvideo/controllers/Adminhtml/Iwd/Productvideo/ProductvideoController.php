<?php
class IWD_Productvideo_Adminhtml_Iwd_Productvideo_ProductvideoController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/iwdall/iwd_productvideo/productmanager');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('multimedia')
            ->_title($this->__('IWD - Product Video'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_productvideo')->__('Product Video'),
            Mage::helper('iwd_productvideo')->__('Product Video')
        );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('iwd_productvideo/adminhtml_productvideo'));
        $this->renderLayout();
    }

    public function update_video_positionAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $videoIDs = explode(',', $data['video_ids']);
            $result['status'] = Mage::getModel('iwd_productvideo/productvideo')
                ->updateVideoPositionByProduct($data['product_id'], $videoIDs);
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }


    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_productvideo/adminhtml_productvideo_grid')->toHtml()
        );
    }

    public function videogridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_productvideo/adminhtml_tabs_productvideos')->toHtml()
        );
    }
}