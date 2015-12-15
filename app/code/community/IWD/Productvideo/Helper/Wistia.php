<?php
/**
 * Created by PhpStorm.
 * User: Kate
 * Date: 20.04.15
 * Time: 14:05
 */
class IWD_Productvideo_Helper_Wistia extends Mage_Core_Helper_Data
{
    public static $info = array();
    private static $id;
//    private static $links;

    /**
     * @var bool $proxy - Use proxy
     * Can be edited (true, false)
     */
//    private static $proxy = true;

//    public function init($id = null)
//    {
//        self::$data = self::$links = self::$info = null;
//        if (self::$proxy) {
//            $dir = realpath(dirname(__FILE__));
//            self::$proxy_list = is_file($dir . '/proxy.txt') ? file($dir . '/proxy.txt') : array();
//            if (empty(self::$proxy_list)) self::$proxy = false;
//            self::$proxy_attempts = sizeof(self::$proxy_list);
//        }
//        self::$id = $id;
//    }

    public static function getInfo($id) {

        if(empty($id)) die('Enter video id');
        if(!empty(self::$info)) return self::$info;
        # Get video data
        if( $curl = curl_init() ) {
            $wistiaUrl = 'https://api.wistia.com/v1/medias/' . $id . '.json?api_password=' . Mage::getStoreConfig('iwd_productvideo/integration/wistia_password');
            curl_setopt($curl, CURLOPT_URL, $wistiaUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $out = curl_exec($curl);
            if(!$out) return false;
            $out = json_decode($out, true);
            self::$info = $out;
            curl_close($curl);
            return self::$info;
        }
        return false;
    }

    public function getIdFromEmbed($url) {
        $regex = "/iframe\/([\d\w]+)[\?\"]/";
        $matches = array();
        if(preg_match($regex, $url, $matches)) {
            if(!empty($matches)) {
                return $matches[1];
            }
        }
    }
}