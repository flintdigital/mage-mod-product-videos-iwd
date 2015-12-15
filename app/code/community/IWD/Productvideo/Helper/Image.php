<?php
class IWD_Productvideo_Helper_Image extends Mage_Catalog_Block_Product_List
{
    protected $allowImageFormats = array('jpg','jpeg','png','gif');

    public function getImageResize($image, $width=100, $height=100, $zoom_ratio=1)
    {
        $width *= $zoom_ratio;
        $height *= $zoom_ratio;

        $resizeImagePath = Mage::helper('iwd_productvideo')->GetMediaImageResizeTypeDir($width.'x'.$height) . $image;

        //have little image
        if (file_exists($resizeImagePath))
            return Mage::helper('iwd_productvideo')->GetMediaImageUrl($image, $width.'x'.$height);

        //have big image, but haven't little
        $imagePath = Mage::helper('iwd_productvideo')->GetMediaImageDir() . $image;
        if (file_exists($imagePath)) {
            $imageObj = new Varien_Image($imagePath);
            $imageObj->constrainOnly(FALSE);
            $imageObj->keepAspectRatio(FALSE);
            $imageObj->keepFrame(FALSE);
            $imageObj->resize($width, $height);
            $imageObj->save($resizeImagePath);

            return Mage::helper('iwd_productvideo')->GetMediaImageUrl($image, $width.'x'.$height);
        }

        //haven't image
        return '';
    }

    public function LoadYoutubeImage($video_id, $url)
    {
        try{
            $img_youtube_url = 'http://img.youtube.com/vi/' . $url . '/0.jpg';
            $image_title = $url . '.jpg';
            $img_file = file_get_contents($img_youtube_url);
            $file_loc = Mage::helper('iwd_productvideo')->GetMediaImagePath($image_title);
            $file_handler = fopen($file_loc, 'w');
            if (fwrite($file_handler, $img_file) == false) {
                fclose($file_handler);
                return 'error';
            }
            fclose($file_handler);

            //update DB
            $img = Mage::getModel('iwd_productvideo/video')->load($video_id);
            $img->setImage($image_title)->save();
            return $image_title;
        } catch(Exception $e){

        }
        return "";
    }

    public function LoadVimeoImage($video_id, $url)
    {
        try{
            $link = 'http://vimeo.com/api/v2/video/' . $url . '.php';
            $html_returned = unserialize(file_get_contents($link));
            $img_vimeo_url = $html_returned[0]['thumbnail_medium'];
            $img_vimeo_url_parts = explode("/", $img_vimeo_url);
            $image_title = end($img_vimeo_url_parts);
            $img_file = file_get_contents($img_vimeo_url);
            $file_loc = Mage::helper('iwd_productvideo')->GetMediaImagePath($image_title);
            $file_handler = fopen($file_loc, 'w');
            if (fwrite($file_handler, $img_file) == false) {
                fclose($file_handler);
                return $image_title;
            }
            fclose($file_handler);

            //update DB
            $img = Mage::getModel('iwd_productvideo/video')->load($video_id);
            $img->setImage($image_title)->save();

            return $image_title;
        }catch (Exception $e){

        }
        return "";
    }

    public function LoadExternImage($video_id, $video_type, $url)
    {
        if ($video_type == 'youtube')
            return Mage::helper('iwd_productvideo/image')->LoadYoutubeImage($video_id, $url);

        if ($video_type == 'vimeo')
            return Mage::helper('iwd_productvideo/image')->LoadVimeoImage($video_id, $url);

        if($video_type == 'local')
            return 'movie.jpg';

        return '';
    }

    public function UploadImage($fieldId, $fileName = null)
    {
        try {
            $path = Mage::helper('iwd_productvideo')->GetMediaImageDir();
            $uploader = new Varien_File_Uploader($fieldId);
            $uploader->setAllowedExtensions($this->allowImageFormats);
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $uploader->save($path, $fileName);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_productvideo')->__($e->getMessage()));
            return false;
        }

        return true;
    }

    public function DeleteImages($imageName, $directory=null)
    {
        if($directory === null)
            $directory = Mage::helper('iwd_productvideo')->GetMediaImageDir();

        if (!file_exists($directory))
            return false;

        unlink($directory.$imageName);

        $resizedImageDir = Mage::helper('iwd_productvideo')->GetMediaImageResizeDir();

        $resized = scandir($resizedImageDir);
        for($i=2; $i<count($resized); $i++)
        {
            $imagePath = Mage::helper('iwd_productvideo')->GetMediaImageResizeTypeDir($resized[$i]).$imageName;
            if(file_exists($imagePath))
                unlink($imagePath);
        }

        return true;
    }
}