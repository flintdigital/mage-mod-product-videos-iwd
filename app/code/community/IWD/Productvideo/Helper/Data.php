<?php
class IWD_Productvideo_Helper_Data extends Mage_Core_Helper_Data
{
    const BASE_IWD_MEDIA_DIR = 'iwd_video';
    const IMAGE_DIR = 'img';
    const RESIZED_DIR = 'resized';
    const VIDEO_DIR = 'video';

    const XML_PATH_GENERAL_ENABLED = 'iwd_productvideo/general/enabled';
    const XML_PATH_lOCAL_PLAYER = 'iwd_productvideo/video/local_player';

    const TYPE_VIMEO = 'vimeo';
    const TYPE_YOUTUBE = 'youtube';
    const TYPE_LOCAL = 'local';

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_ENABLED, Mage::app()->getStore());
    }

    public function getAllowedVideoFormats()
    {
        return array('mp4', 'ogv', 'ogg', 'webm');
    }

    public function GetVideoUrl($video = null)
    {
        if ($video !== null)
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BASE_IWD_MEDIA_DIR . '/' . self::VIDEO_DIR . '/' . $video;
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BASE_IWD_MEDIA_DIR . '/' . self::VIDEO_DIR . '/';
    }

    public function GetVideoDir()
    {
        $path = Mage::getBaseDir('media') . DS . self::BASE_IWD_MEDIA_DIR . DS;
        if (!file_exists($path))
            mkdir($path, 0777);

        $path .= self::VIDEO_DIR . DS;
        if (!file_exists($path))
            mkdir($path, 0777);

        return $path;
    }

    public function isAvailableVersion(){
        $mage = new Mage();
        if (!is_callable(array($mage, 'getEdition'))){
            $edition = 'Community';
        }else{
            $edition = Mage::getEdition();
        }
        unset($mage);

        if ($edition=='Enterprise' && $this->_version=='CE'){
            return false;
        }
        return true;
    }

    /**
     * .../media/iwd_video/img/
     * @return string
     */
    public function GetMediaImageDir()
    {
        $path = Mage::getBaseDir('media') . DS . self::BASE_IWD_MEDIA_DIR . DS;
        if (!file_exists($path))
            mkdir($path, 0777);

        $path .= self::IMAGE_DIR . DS;
        if (!file_exists($path))
            mkdir($path, 0777);

        return $path;
    }

    public function GetMediaImageRelativePath()
    {
        return str_replace(Mage::getBaseDir('base'), '', $this->GetMediaImageDir());
    }

    /**
     * .../media/iwd_video/img/<image_name>
     * @param $image_name
     * @return string
     */
    public function GetMediaImagePath($imageName)
    {
        return $this->GetMediaImageDir() . $imageName;
    }

    /**
     * .../media/iwd_video/video/<video_name>
     * @param $image_name
     * @return string
     */
    public function GetMediaVideoPath($videoName)
    {
        return $this->GetVideoDir() . $videoName;
    }

    /**
     * .../media/iwd_video/img/resized/
     * @return string
     */
    public function GetMediaImageResizeDir()
    {
        $path = $this->GetMediaImageDir();

        $path .= self::RESIZED_DIR . DS;
        if (!file_exists($path))
            mkdir($path, 0777);

        return $path;
    }

    /**
     * .../media/iwd_video/img/resized/<Type resized image>/
     * @param $type
     * @return string
     */
    public function GetMediaImageResizeTypeDir($type)
    {
        $path = $this->GetMediaImageResizeDir();

        $path .= $type . DS;
        if (!file_exists($path))
            mkdir($path, 0777);

        return $path;
    }

    public function GetMediaImageUrl($imageTitle, $type = null)
    {
        if ($type === null)
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BASE_IWD_MEDIA_DIR . '/' . self::IMAGE_DIR . '/' . $imageTitle;
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BASE_IWD_MEDIA_DIR . '/' . self::IMAGE_DIR . '/' . self::RESIZED_DIR . '/' . $type . '/' . $imageTitle;
    }

    public function getVideoType($extension)
    {
        switch ($extension) {
            case 'mp4':
                return 'type="video/mp4"';
            case 'webm':
                return 'type="video/webm"';
            case 'ogv':
            case 'ogg':
                return 'type="video/ogg"';
            case 'flv':
                return 'type="video/x-flv"';
            case 'm3u8':
                return 'type="application/x-mpegURL"';
            case 'ts':
                return 'type="video/MP2T"';
            case '3gp':
                return 'type="video/3gpp"';
            case 'mov':
                return 'type="video/quicktime"';
            case 'avi':
                return 'type="video/x-msvideo"';
            case 'wmv':
                return 'type="video/x-ms-wmv"';
        }
        return "";
    }

    public function getFileExtensionByFilename($file)
    {
        $url_parts = explode('.', $file);
        return end($url_parts);
    }

    public function getFileExtensionByUrl($url)
    {
        $url = explode("?", $url);
        $path_parts = pathinfo($url[0]);
        if (isset($path_parts['extension']))
            return $path_parts['extension'];
        return "";
    }

    public function getVideoSource($video)
    {
        $type = $video['video_type'];
        $url = $video['url'];

        try {
            switch ($type) {
                case self::TYPE_LOCAL:
                    $extension = $this->getFileExtensionByFilename($url);
                    $link = $this->getVideoUrl($url);
                    if (!empty($link))
                        return '<source src="' . $link . '" ' . $this->getVideoType($extension) . '/>';
                    break;

                case self::TYPE_YOUTUBE:
                    $youtube_links = Mage::helper('iwd_productvideo/youtube')->getVideoLink($url);
                    if (!empty($youtube_links)) {
                        $html = '';
                        foreach ($youtube_links as $link)
                            $html .= '<source src="' . $link[2] . '" type="video/' . $link[0] . '"/>';
                        return $html;
                    }
                    break;

                case self::TYPE_VIMEO:
                    $link = Mage::helper('iwd_productvideo/vimeo')->getVimeoDirectUrl($url);
                    if (!empty($link)) {
                        $extension = $this->getFileExtensionByUrl($link);
                        return '<source src="' . $link . '" ' . $this->getVideoType($extension) . ' />';
                    }
                    break;
            }
        } catch (Exception $e) {
            return "";
        }
        return "";
    }

    public function isLocalVideoPlayer($video)
    {
        if ($video['video_type'] == 'local') return true;
        return false;
//        remove ability to use html5 player since youtube already giving html5 player
//        return Mage::getStoreConfig(self::XML_PATH_lOCAL_PLAYER, Mage::app()->getStore());
    }

    public function GetImageUrl($image, $width = 100, $height = 100, $zoom_ratio = 1)
    {
        if (!file_exists($this->GetMediaImagePath($image)))
            return Mage::helper('iwd_productvideo/image')->getImageResize('movie.jpg', $width, $height, $zoom_ratio);
        return Mage::helper('iwd_productvideo/image')->getImageResize($image, $width, $height, $zoom_ratio);
    }

    public function getMaxUploadFileSize()
    {
        return ini_get('upload_max_filesize') < ini_get('post_max_size') ? ini_get('upload_max_filesize') : ini_get('post_max_size');
    }

    public function UploadVideo($fieldId, $fileName = null)
    {
        try {
            $path = $this->GetVideoDir();
            $uploader = new Varien_File_Uploader($fieldId);

            $allowed_video_formats = $this->getAllowedVideoFormats();
            $uploader->setAllowedExtensions($allowed_video_formats);
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

    protected $_version = 'CE';
}