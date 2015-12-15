<?php
class IWD_Productvideo_Adminhtml_Iwd_Productvideo_VideoController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/iwdall/iwd_productvideo/videomanager');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('multimedia')
            ->_title($this->__('IWD - Product Video Manager'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_productvideo')->__('Product Video Manager'),
            Mage::helper('iwd_productvideo')->__('Product Video Manager')
        );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD - Add Video'));

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Video Name Manager'), Mage::helper('adminhtml')->__('Video Names Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Video Name'), Mage::helper('adminhtml')->__('Video Name'));

        $this->getLayout()
            ->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit'));
        $this->_addLeft($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('video_id');
        $model = Mage::getModel('iwd_productvideo/video')->load($id);
        // edit
        if ($model->getVideoId()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)){
                $data = is_array($data) ? $data : (is_object($data) ? $data->getData() : array());
                $model->setData($data);
            }

            Mage::register('video_data', $model);

            $this->loadLayout()
                ->_setActiveMenu('system')
                ->_title($this->__('IWD - Edit Video'));

            $this->_addContent($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit'));
            $this->_addLeft($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit_tabs'));

            $this->renderLayout();
        } // new
        else {
            $this->loadLayout()
                ->_setActiveMenu('system')
                ->_title($this->__('IWD - Add Video'));

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Video Name Manager'), Mage::helper('adminhtml')->__('Video Names Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Video Name'), Mage::helper('adminhtml')->__('Video Name'));

            $this->getLayout()
                ->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit'));
            $this->_addLeft($this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $removeOldImage = false;
            try {
                #load local video
                if (isset($_FILES['url']['name']) && !empty($_FILES['url']['name'])) {
                    $fileParts = pathinfo($_FILES['url']['name']);
                    $data['url'] = uniqid() . '.' . strtolower($fileParts['extension']);
                    $result = Mage::helper('iwd_productvideo')->UploadVideo('url', $data['url']);

                    if ($result !== true) {
                        $this->_redirect('*/*/new');
                        return;
                    }
                }

                #load image
                if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                    $fileParts = pathinfo($_FILES['image']['name']);
                    $data['image'] = uniqid() . '.' . strtolower($fileParts['extension']);
                    $result = Mage::helper('iwd_productvideo/image')->UploadImage('image', $data['image']);
                    if ($result !== true) {
                        unset($data['image']);
                    } else {
                        $removeOldImage = true;
                    }
                } else {
                    unset($data['image']);
                }

                #save product_video
                if (isset($data['selected_products_IDs']))
                    Mage::getModel('iwd_productvideo/productvideo')->SaveProductVideoLinks($data['selected_products_IDs'], $data['video_id']);

                #save video info
                $model = Mage::getModel('iwd_productvideo/video');
                if ($this->getRequest()->getParam('video_id')){
                    $model->load($this->getRequest()->getParam('video_id'));
                }

                if (isset($data['video_type']) && in_array($data['video_type'], array('youtube', 'vimeo'))) {
                    $helper = Mage::helper('iwd_productvideo/' . $data['video_type']);
                    $matches = array();
                    $regex = $helper->getUrlParseRegex();
                    $index = $helper->getRegexMatchIndexOfVideoId();
                    if (preg_match($regex, $data['url'], $matches)) {
                        if (!empty($matches) && !empty($matches[$index])) {
                            $data['url'] = $matches[$index];
                        }
                    }
                }

                $data['video_store_view'] = (isset($data['video_store_view_single'])) ? serialize(array("0")) : serialize($data['video_store_view']);

                // on new image upload - delete old image
                if($removeOldImage) {
                    $oldImage = $model->getImage();
                    $directory = Mage::helper('iwd_productvideo')->GetMediaImageDir() . 'resized';
                    foreach(scandir($directory) as $file) {
                        if($file == '.' || $file == '..') continue;
                        $dir = $directory . DS . $file . DS;
                        if(is_dir($dir)) {
                            $resizedImage = $dir . $oldImage;
                            if(file_exists($resizedImage)) {
                                @unlink($resizedImage);
                            }
                        }
                    }
                    @unlink(Mage::helper('iwd_productvideo')->GetMediaImagePath($oldImage));
                }
                // END on new image upload - delete old image

                $model->setData($data)->save();

                if (!$model->getImage()){
                    Mage::helper('iwd_productvideo/image')->LoadExternImage($model->getVideoId(), $model->getVideoType(), $model->getUrl());
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('iwd_productvideo')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('video_id' => $model->getVideoId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($video_id = $this->getRequest()->getParam('video_id')) {
                    $data = Mage::getModel('iwd_productvideo/video')->load($video_id);
                    Mage::getSingleton('adminhtml/session')->setFormData($data->getData());
                    $this->_redirect('*/*/edit', array('video_id' => $video_id));
                    return;
                }
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_productvideo')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function massStatusAction()
    {
        $videoIds = $this->getRequest()->getParam('video');
        $status = (int)$this->getRequest()->getParam('status');

        if (!is_array($videoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($videoIds as $videoId) {
                    $video = Mage::getModel('iwd_productvideo/video')->load($videoId, 'video_id');
                    $video->setVideoStatus($status)->save();
                }
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) have been updated.', count($videoIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $videoIds = $this->getRequest()->getParam('video');
        if (!is_array($videoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($videoIds as $videoId) {
                    $model = Mage::getModel('iwd_productvideo/video')->load($videoId, 'video_id');

                    #delete all images (original, resized, ...)
                    Mage::helper('iwd_productvideo/image')->DeleteImages($model->getImage());

                    #delete local video
                    if ($model->getVideoType() == 'local')
                        unlink(Mage::helper('iwd_productvideo')->GetMediaVideoPath($model->getUrl()));

                    #delete from DB (all link was delete too)
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($videoIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        try {
            $videoId = $this->getRequest()->getParam('video');

            $model = Mage::getModel('iwd_productvideo/video')->load($videoId, 'video_id');

            #delete all images (original, resized, ...)
            Mage::helper('iwd_productvideo/image')->DeleteImages($model->getImage());

            #delete local video
            if ($model->getVideoType() == 'local')
                unlink(Mage::helper('iwd_productvideo')->GetMediaVideoPath($model->getUrl()));

            #delete from DB (all link was delete too in trigger)
            $model->delete();

            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('adminhtml')->__('Video was successfully deleted'));
        } catch
        (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_productvideo/adminhtml_video_grid')->toHtml()
        );
    }

    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function grid_pvAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_productvideo/adminhtml_productvideo_edit_tab_form')->toHtml()
        );
    }

    public function attachVideoToProductAction()
    {
        try {
            $responseData = array('success' => 1);
            $params = $this->getRequest()->getParams();
            $id_product = $params['product_id'];
            $attach = $params['attach'];
            $id_video = $params['video_id'];

            if ($attach) {
                Mage::getModel('iwd_productvideo/productvideo')
                    ->setData('video_id', $id_video)
                    ->setData('video_position', 0)
                    ->setData('product_id', $id_product)
                    ->save();
            } else {
                $collection = Mage::getModel('iwd_productvideo/productvideo')->getCollection()
                    ->addFieldToFilter('video_id', $id_video)
                    ->addFieldToFilter('product_id', $id_product);

                foreach ($collection as $item) {
                    $item->delete();
                }
            }
        } catch (Exception $e) {
            $responseData['error'] = $e->getMessage();
            Mage::logException($e);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }
}