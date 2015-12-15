<?php
class IWD_Productvideo_Helper_Vimeo extends Mage_Core_Helper_Abstract
{
    /**
     * @var array Vimeo prioritet quality video to show
     */
    public $vimeoQualityPrioritet = array('sd', 'hd', 'mobile');

    /**
     * @var string Vimeo video codec
     */
    public $vimeoVideoCodec = 'h264';

    /**
     * Get direct URL to Vimeo video file
     *
     * @param string $url to video on Vimeo
     * @return string file URL
     */
    public function getVimeoDirectUrl($id)
    {
        $url = 'http://vimeo.com/' . $id;
        $page = $this->getRemoteContent($url);
        $dom = new DOMDocument("1.0", "utf-8");
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $page);
        $xPath = new DOMXpath($dom);
        $video = $xPath->query('//div[@class = "player_container"]/div[@class = "player"]')->item(0);
        if ($video) {
            $json = json_decode($this->getRemoteContent($video->getAttribute('data-config-url')));
            if (property_exists($json, 'message')) {
                return false;
            }
            $videoObject = null;
            if (property_exists($json->request->files, $this->vimeoVideoCodec)) {
                $videoObject = $this->getQualityVideo($json->request->files->{$this->vimeoVideoCodec});
            }
            if ($videoObject) {
                return $videoObject->url;
            }
        }
        return false;
    }

    /**
     * Get remote content by URL
     *
     * @param string $url remote page URL
     * @return string result content
     */
    private function getRemoteContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'spider');
        $content = curl_exec($ch);

        curl_close($ch);

        return $content;
    }

    /**
     * Get vimeo video object
     *
     * @param stdClass $files object of Vimeo files
     * @return stdClass Video file object
     */
    private function getQualityVideo($files)
    {
        $video = null;
        foreach ($this->vimeoQualityPrioritet as $quality) {
            if (property_exists($files, $quality)) {
                $video = $files->{$quality};
                break;
            }
        }
        if (!$video) {
            foreach (get_object_vars($files) as $file) {
                $video = $file;
                break;
            }
        }
        return $video;
    }

    public function getUrlParseRegex()
    {
        return "/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/";
    }

    public function getRegexMatchIndexOfVideoId()
    {
        return 5;
    }
}